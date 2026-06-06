<?php

namespace Tests\Unit;

use App\Models\Equipo;
use PHPUnit\Framework\TestCase;

class EquipoTest extends TestCase
{
    public function test_it_resolves_flag_html_from_team_code(): void
    {
        $equipo = new Equipo([
            'name' => 'Mexico',
            'code' => 'MEX',
            'grupo' => 'A',
        ]);

        $this->assertSame(
            '<img src="https://api.fifa.com/api/v3/picture/flags-sq-1/MEX" alt="Bandera de Mexico" class="h-4 w-4 rounded-sm object-cover" loading="lazy" decoding="async">',
            $equipo->flagEmojiHtml(),
        );
    }

    public function test_it_does_not_render_flags_for_invalid_codes(): void
    {
        $equipo = new Equipo([
            'name' => 'Invalid',
            'code' => 'MEXICO',
            'grupo' => 'A',
        ]);

        $this->assertSame('', $equipo->flagEmojiHtml());
    }
}
