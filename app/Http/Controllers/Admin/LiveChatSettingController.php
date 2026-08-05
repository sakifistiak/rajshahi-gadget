<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LiveChatSettingController extends Controller
{
    public function index(): View
    {
        $settings = [
            'live_chat_enabled' => SiteSetting::getValue('live_chat_enabled', '1'),
            'live_chat_whatsapp_enabled' => SiteSetting::getValue('live_chat_whatsapp_enabled', '1'),
            'live_chat_whatsapp_number' => SiteSetting::getValue('live_chat_whatsapp_number', SiteSetting::getValue('whatsapp_number', '8801700000001')),
            'live_chat_messenger_enabled' => SiteSetting::getValue('live_chat_messenger_enabled', '0'),
            'live_chat_messenger_url' => SiteSetting::getValue('live_chat_messenger_url', ''),
            'live_chat_call_enabled' => SiteSetting::getValue('live_chat_call_enabled', '1'),
            'live_chat_call_number' => SiteSetting::getValue('live_chat_call_number', SiteSetting::getValue('site_phone', '+8801700000000')),
            'live_chat_toggle_color' => SiteSetting::getValue('live_chat_toggle_color', '#24272c'),
            'live_chat_whatsapp_color' => SiteSetting::getValue('live_chat_whatsapp_color', '#25D366'),
            'live_chat_messenger_color' => SiteSetting::getValue('live_chat_messenger_color', '#0084FF'),
            'live_chat_call_color' => SiteSetting::getValue('live_chat_call_color', '#4f46e5'),
        ];

        return view('admin.live-chat-settings.index', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $whatsappNumber = preg_replace('/[^0-9]/', '', (string) $request->input('live_chat_whatsapp_number', ''));
        $callNumber = preg_replace('/[^0-9+]/', '', (string) $request->input('live_chat_call_number', ''));

        $request->merge([
            'live_chat_whatsapp_number' => $whatsappNumber !== '' ? $whatsappNumber : null,
            'live_chat_call_number' => $callNumber !== '' ? $callNumber : null,
        ]);

        $request->validate([
            'live_chat_whatsapp_number' => ['nullable', 'regex:/^[0-9]{7,15}$/'],
            'live_chat_messenger_url' => 'nullable|url|max:500',
            'live_chat_call_number' => 'nullable|string|max:30',
            'live_chat_toggle_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'live_chat_whatsapp_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'live_chat_messenger_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'live_chat_call_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);

        foreach (['live_chat_whatsapp_number', 'live_chat_messenger_url', 'live_chat_call_number', 'live_chat_toggle_color', 'live_chat_whatsapp_color', 'live_chat_messenger_color', 'live_chat_call_color'] as $key) {
            SiteSetting::updateOrCreate(['key' => $key], ['value' => $request->input($key)]);
        }

        foreach (['live_chat_enabled', 'live_chat_whatsapp_enabled', 'live_chat_messenger_enabled', 'live_chat_call_enabled'] as $key) {
            SiteSetting::updateOrCreate(['key' => $key], ['value' => $request->has($key) ? '1' : '0']);
        }

        return redirect()->back()->with('success', 'Live chat settings updated successfully!');
    }
}
