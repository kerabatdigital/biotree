<?php

namespace App\Support;

/**
 * Turns the editor's high-level theme inputs into the concrete values the
 * public page renders. One source of truth for editor, live preview and page.
 */
class ThemeBuilder
{
    public static function build(array $in): array
    {
        $bg = $in['bg'] ?? '#0a0a0a';
        $bgEnd = $in['bg_end'] ?? $bg;
        $text = $in['text'] ?? '#ffffff';
        $accent = $in['accent'] ?? '#34d399';
        $style = in_array($in['button_style'] ?? 'soft', ['soft', 'solid', 'outline'], true) ? $in['button_style'] : 'soft';
        $radius = $in['button_radius'] ?? '16px';
        $font = $in['font'] ?? 'figtree';
        $avatarShape = in_array($in['avatar_shape'] ?? 'circle', ['circle', 'rounded', 'square'], true) ? $in['avatar_shape'] : 'circle';

        [$btnBg, $btnText, $btnBorder] = match ($style) {
            'solid' => [$accent, self::contrast($accent), $accent],
            'outline' => ['transparent', $text, self::rgba($text, 0.28)],
            default => [self::rgba($text, 0.09), $text, self::rgba($text, 0.12)],
        };

        return [
            // inputs (preserved so the editor can reload the exact choices)
            'preset' => $in['preset'] ?? 'custom',
            'bg' => $bg,
            'bg_end' => $bgEnd,
            'text' => $text,
            'accent' => $accent,
            'button_style' => $style,
            'button_radius' => $radius,
            'avatar_shape' => $avatarShape,
            'font' => $font,
            // derived (consumed by the public page)
            'muted' => self::rgba($text, 0.58),
            'button_bg' => $btnBg,
            'button_text' => $btnText,
            'button_border' => $btnBorder,
        ];
    }

    protected static function rgb(string $hex): array
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }
        if (strlen($hex) !== 6 || ! ctype_xdigit($hex)) {
            return [255, 255, 255];
        }

        return [hexdec(substr($hex, 0, 2)), hexdec(substr($hex, 2, 2)), hexdec(substr($hex, 4, 2))];
    }

    protected static function rgba(string $hex, float $alpha): string
    {
        [$r, $g, $b] = self::rgb($hex);

        return "rgba($r, $g, $b, $alpha)";
    }

    /**
     * Pick black or white for legible text over the given colour.
     */
    protected static function contrast(string $hex): string
    {
        [$r, $g, $b] = self::rgb($hex);
        $luminance = (0.2126 * $r + 0.7152 * $g + 0.0722 * $b) / 255;

        return $luminance > 0.6 ? '#0a0a0a' : '#ffffff';
    }
}
