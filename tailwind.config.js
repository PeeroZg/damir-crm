/**
 * Damir CRM — Tailwind CSS v3 config
 * Fonts: Bricolage Grotesque (display), Geist (UI), Geist Mono (IDs, numbers in code).
 */
import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './resources/views/**/*.blade.php',
    './resources/js/**/*.js',
    './app/Enums/**/*.php',
  ],
  // Stage/status colours are composed dynamically in Blade (bg-{{ $stage }}-solid), so keep them in the build.
  safelist: [
    { pattern: /^(bg|text|ring|border)-(lead|qualified|proposal|negotiation|won|lost|draft|sent|expired)-(bg|fg|solid)$/ },
    { pattern: /^pill-(lead|qualified|proposal|negotiation|won|lost|draft|sent|expired|volt|neutral)$/ },
    { pattern: /^bg-(lead|qualified|proposal|negotiation|won|lost|draft|sent|expired)-(bg|solid)\/(50|60)$/ },
    { pattern: /^avatar-(sky|green|indigo|violet|amber|rose|volt)$/ },
  ],
  darkMode: 'class',
  theme: {
    extend: {
      fontFamily: {
        sans: ['Geist', ...defaultTheme.fontFamily.sans],
        display: ['"Bricolage Grotesque"', 'Geist', ...defaultTheme.fontFamily.sans],
        mono: ['"Geist Mono"', ...defaultTheme.fontFamily.mono],
      },
      fontSize: {
        // [size, { lineHeight, letterSpacing }]
        '2xs': ['11px', { lineHeight: '14px', letterSpacing: '0.04em' }],
        xs: ['12px', { lineHeight: '16px' }],
        sm: ['13px', { lineHeight: '18px' }],
        base: ['14px', { lineHeight: '20px' }],
        md: ['15px', { lineHeight: '22px' }],
        lg: ['17px', { lineHeight: '24px' }],
        xl: ['20px', { lineHeight: '26px', letterSpacing: '-0.01em' }],
        '2xl': ['24px', { lineHeight: '30px', letterSpacing: '-0.015em' }],
        '3xl': ['30px', { lineHeight: '34px', letterSpacing: '-0.02em' }],
        '4xl': ['38px', { lineHeight: '40px', letterSpacing: '-0.025em' }],
        '5xl': ['52px', { lineHeight: '52px', letterSpacing: '-0.03em' }],
      },
      colors: {
        // Neutrals — a warm graphite. 500 is the lightest grey allowed for text.
        ink: {
          0: '#FFFFFF',
          25: '#FBFBFA',
          50: '#F4F4F1',
          100: '#EDEDE9',
          150: '#E5E5E0',
          200: '#D9D9D3',
          300: '#C4C4BD',
          400: '#85857E',
          500: '#6B6B65',
          600: '#55554F',
          700: '#3F3F3B',
          800: '#2A2A27',
          900: '#1A1A18',
          950: '#0F0F0E',
        },
        // Volt — the one brand accent. Fill only (ink text on top); never text on white.
        volt: {
          100: '#F5FFD1',
          200: '#ECFF9E',
          300: '#E2FE72',
          400: '#D4FA4E',
          500: '#BDE52A',
          600: '#93B400',
          700: '#5E7700',
        },
        // Deal stages: { bg tint, fg text (AA on tint + white), solid (dots/bars, 3:1 on white) }
        lead: { bg: '#E5F3FB', fg: '#0B5C85', solid: '#1B8FC6' },
        qualified: { bg: '#ECEDFE', fg: '#3B3FC4', solid: '#5B61F0' },
        proposal: { bg: '#F4EAFD', fg: '#7B2DB3', solid: '#A35BE0' },
        negotiation: { bg: '#FFF1D9', fg: '#8A4A00', solid: '#C27A00' },
        won: { bg: '#E2F6E9', fg: '#146B3A', solid: '#1F9A55' },
        lost: { bg: '#FDE8EB', fg: '#A8213F', solid: '#E5486A' },
        // Offer statuses (Accepted = won, Rejected = lost)
        draft: { bg: '#EDEDE9', fg: '#4A4A45', solid: '#85857E' },
        sent: { bg: '#E6EEFF', fg: '#2547C9', solid: '#4B74F5' },
        expired: { bg: '#F5ECE4', fg: '#7A4520', solid: '#B9773F' },
      },
      borderRadius: {
        sm: '6px',
        md: '8px',
        lg: '12px',
        xl: '16px',
        '2xl': '20px',
      },
      boxShadow: {
        xs: '0 1px 2px rgba(15,15,14,0.05)',
        card: '0 1px 2px rgba(15,15,14,0.04), 0 1px 0 rgba(15,15,14,0.02)',
        pop: '0 12px 32px -8px rgba(15,15,14,0.18), 0 2px 6px rgba(15,15,14,0.06)',
        drag: '0 18px 40px -10px rgba(15,15,14,0.30), 0 4px 10px rgba(15,15,14,0.08)',
        modal: '0 30px 80px -20px rgba(15,15,14,0.40), 0 0 0 1px rgba(15,15,14,0.06)',
        'volt-glow': '0 0 0 4px rgba(212,250,78,0.55)',
      },
      keyframes: {
        'toast-in': { '0%': { opacity: 0, transform: 'translateY(8px) scale(.98)' }, '100%': { opacity: 1, transform: 'none' } },
        'pop-in': { '0%': { opacity: 0, transform: 'scale(.97)' }, '100%': { opacity: 1, transform: 'none' } },
        confetti: { '0%': { transform: 'translate(0,0) rotate(0)', opacity: 1 }, '100%': { transform: 'translate(var(--x), var(--y)) rotate(540deg)', opacity: 0 } },
      },
      animation: {
        'toast-in': 'toast-in .22s cubic-bezier(.2,.8,.2,1)',
        'pop-in': 'pop-in .16s cubic-bezier(.2,.8,.2,1)',
        confetti: 'confetti .9s cubic-bezier(.1,.7,.3,1) forwards',
      },
    },
  },
  plugins: [forms({ strategy: 'class' })],
};
