<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Auth\Login as BaseLogin;

/**
 * Halaman login pengurus — mengikuti mockup: judul "Login Pengurus",
 * input email & kata sandi berikon, checkbox "Ingat saya".
 */
class Login extends BaseLogin
{
    public function getHeading(): string
    {
        return 'Login Pengurus';
    }

    public function getSubheading(): ?string
    {
        return 'Sistem Informasi Manajemen Masjid (SIMASJID)';
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label('Alamat Email')
            ->email()
            ->required()
            ->autocomplete()
            ->autofocus()
            ->prefixIcon('heroicon-o-envelope')
            ->placeholder('nama@email.com')
            ->extraInputAttributes(['tabindex' => 1]);
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label('Kata Sandi')
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->autocomplete('current-password')
            ->required()
            ->prefixIcon('heroicon-o-lock-closed')
            ->placeholder('••••••••')
            ->extraInputAttributes(['tabindex' => 2]);
    }

    protected function getRememberFormComponent(): Component
    {
        return parent::getRememberFormComponent()->label('Ingat saya');
    }
}
