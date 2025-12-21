<?php

namespace App\Http\Controllers;

use App\Models\ReceiptSetting;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * ReceiptSettingController - Mengelola pengaturan struk penjualan.
 *
 * Controller ini menangani konfigurasi struk:
 * - Informasi toko (nama, alamat, telepon, email)
 * - Teks header, footer, dan terima kasih
 * - Upload dan hapus logo toko
 * - Toggle visibility elemen struk
 * - Preview struk
 *
 * @package App\Http\Controllers
 * @author  Star Frozen Team
 * @version 1.0.0
 */
class ReceiptSettingController extends Controller
{
    /**
     * Menampilkan form pengaturan struk.
     *
     * @return View
     */
    public function index(): View
    {
        $settings = ReceiptSetting::current();
        return view('manager.receipt-settings.index', compact('settings'));
    }

    /**
     * Mengupdate pengaturan struk.
     *
     * Memproses:
     * - Validasi input
     * - Upload logo baru (hapus yang lama jika ada)
     * - Handle checkbox values
     * - Pencatatan activity log dengan old/new values
     *
     * @param  Request $request Request dengan data setting
     * @return RedirectResponse
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'store_name' => 'required|string|max:255',
            'store_address' => 'nullable|string|max:1000',
            'store_phone' => 'nullable|string|max:50',
            'store_email' => 'nullable|email|max:255',
            'header_text' => 'nullable|string|max:500',
            'footer_text' => 'nullable|string|max:500',
            'thank_you_text' => 'nullable|string|max:255',
            'receipt_width' => 'nullable|string|max:20',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'show_logo' => 'nullable|boolean',
            'show_address' => 'nullable|boolean',
            'show_phone' => 'nullable|boolean',
            'show_cashier_name' => 'nullable|boolean',
            'show_thank_you' => 'nullable|boolean',
        ]);

        $settings = ReceiptSetting::current();
        
        // Store old values for logging
        $oldValues = $settings->toArray();

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($settings->logo && Storage::disk('public')->exists($settings->logo)) {
                Storage::disk('public')->delete($settings->logo);
            }
            
            $logoPath = $request->file('logo')->store('receipt', 'public');
            $validated['logo'] = $logoPath;
        }

        // Handle checkbox values
        $validated['show_logo'] = $request->has('show_logo');
        $validated['show_address'] = $request->has('show_address');
        $validated['show_phone'] = $request->has('show_phone');
        $validated['show_cashier_name'] = $request->has('show_cashier_name');
        $validated['show_thank_you'] = $request->has('show_thank_you');

        $settings->update($validated);

        // Log activity
        ActivityLogService::logUpdate(
            'settings',
            'Mengubah pengaturan struk penjualan',
            $settings,
            $oldValues,
            $settings->fresh()->toArray()
        );

        return redirect()->route('manager.receipt-settings.index')
            ->with('success', 'Pengaturan struk berhasil disimpan.');
    }

    /**
     * Menghapus logo dari pengaturan struk.
     *
     * @return RedirectResponse
     */
    public function removeLogo(): RedirectResponse
    {
        $settings = ReceiptSetting::current();
        
        if ($settings->logo && Storage::disk('public')->exists($settings->logo)) {
            Storage::disk('public')->delete($settings->logo);
        }
        
        $settings->update(['logo' => null]);

        return redirect()->route('manager.receipt-settings.index')
            ->with('success', 'Logo berhasil dihapus.');
    }

    /**
     * Preview the receipt with current settings.
     */
    public function preview()
    {
        $settings = ReceiptSetting::current();
        
        // Sample transaction data for preview
        $transaction = [
            'invoice_number' => 'INV-PREVIEW-001',
            'checkout_time' => now()->format('Y-m-d H:i:s'),
            'created_at' => now(),
            'items' => [
                ['name' => 'Produk Contoh 1', 'quantity' => 2, 'price' => 15000],
                ['name' => 'Produk Contoh 2', 'quantity' => 1, 'price' => 25000],
                ['name' => 'Produk Contoh 3', 'quantity' => 3, 'price' => 10000],
            ],
            'subtotal' => 85000,
            'discount_amount' => 5000,
            'tax_amount' => 0,
            'total' => 80000,
            'payment_method' => 'cash',
            'payment_amount' => 100000,
        ];
        
        $cashier = auth()->user();
        
        return view('manager.receipt-settings.preview', compact('settings', 'transaction', 'cashier'));
    }
}
