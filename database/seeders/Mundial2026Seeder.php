<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Mundial2026Seeder extends Seeder
{
    /**
     * Calendario oficial fase de grupos (horario ET → UTC).
     * Fuente: sorteo y fixture FIFA / World Cup 2026 (dic. 2025).
     */
    public function run(): void
    {
        $now = now();

        // Eliminar en orden correcto respetando foreign keys
        DB::table('predicciones')->delete();
        DB::table('partidos')->delete();
        DB::table('equipos')->delete();

        $equipos = [


            ['id' => 1, 'name' => 'México', 'code' => 'MEX', 'grupo' => 'A'],
            ['id' => 2, 'name' => 'Sudáfrica', 'code' => 'RSA', 'grupo' => 'A'],
            ['id' => 3, 'name' => 'Corea del Sur', 'code' => 'KOR', 'grupo' => 'A'],
            ['id' => 4, 'name' => 'Chequia', 'code' => 'CZE', 'grupo' => 'A'],

            ['id' => 5, 'name' => 'Canadá', 'code' => 'CAN', 'grupo' => 'B'],
            ['id' => 6, 'name' => 'Bosnia y Herzegovina', 'code' => 'BIH', 'grupo' => 'B'],
            ['id' => 7, 'name' => 'Catar', 'code' => 'QAT', 'grupo' => 'B'],
            ['id' => 8, 'name' => 'Suiza', 'code' => 'SUI', 'grupo' => 'B'],

            ['id' => 9, 'name' => 'Brasil', 'code' => 'BRA', 'grupo' => 'C'],
            ['id' => 10, 'name' => 'Marruecos', 'code' => 'MAR', 'grupo' => 'C'],
            ['id' => 11, 'name' => 'Haití', 'code' => 'HAI', 'grupo' => 'C'],
            ['id' => 12, 'name' => 'Escocia', 'code' => 'SCO', 'grupo' => 'C'],

            ['id' => 13, 'name' => 'Estados Unidos', 'code' => 'USA', 'grupo' => 'D'],
            ['id' => 14, 'name' => 'Paraguay', 'code' => 'PAR', 'grupo' => 'D'],
            ['id' => 15, 'name' => 'Australia', 'code' => 'AUS', 'grupo' => 'D'],
            ['id' => 16, 'name' => 'Turquía', 'code' => 'TUR', 'grupo' => 'D'],

            ['id' => 17, 'name' => 'Alemania', 'code' => 'GER', 'grupo' => 'E'],
            ['id' => 18, 'name' => 'Curazao', 'code' => 'CUW', 'grupo' => 'E'],
            ['id' => 19, 'name' => 'Costa de Marfil', 'code' => 'CIV', 'grupo' => 'E'],
            ['id' => 20, 'name' => 'Ecuador', 'code' => 'ECU', 'grupo' => 'E'],

            ['id' => 21, 'name' => 'Países Bajos', 'code' => 'NED', 'grupo' => 'F'],
            ['id' => 22, 'name' => 'Japón', 'code' => 'JPN', 'grupo' => 'F'],
            ['id' => 23, 'name' => 'Suecia', 'code' => 'SWE', 'grupo' => 'F'],
            ['id' => 24, 'name' => 'Túnez', 'code' => 'TUN', 'grupo' => 'F'],

            ['id' => 25, 'name' => 'Bélgica', 'code' => 'BEL', 'grupo' => 'G'],
            ['id' => 26, 'name' => 'Egipto', 'code' => 'EGY', 'grupo' => 'G'],
            ['id' => 27, 'name' => 'Irán', 'code' => 'IRN', 'grupo' => 'G'],
            ['id' => 28, 'name' => 'Nueva Zelanda', 'code' => 'NZL', 'grupo' => 'G'],

            ['id' => 29, 'name' => 'España', 'code' => 'ESP', 'grupo' => 'H'],
            ['id' => 30, 'name' => 'Cabo Verde', 'code' => 'CPV', 'grupo' => 'H'],
            ['id' => 31, 'name' => 'Arabia Saudita', 'code' => 'KSA', 'grupo' => 'H'],
            ['id' => 32, 'name' => 'Uruguay', 'code' => 'URU', 'grupo' => 'H'],

            ['id' => 33, 'name' => 'Francia', 'code' => 'FRA', 'grupo' => 'I'],
            ['id' => 34, 'name' => 'Senegal', 'code' => 'SEN', 'grupo' => 'I'],
            ['id' => 35, 'name' => 'Irak', 'code' => 'IRQ', 'grupo' => 'I'],
            ['id' => 36, 'name' => 'Noruega', 'code' => 'NOR', 'grupo' => 'I'],

            ['id' => 37, 'name' => 'Argentina', 'code' => 'ARG', 'grupo' => 'J'],
            ['id' => 38, 'name' => 'Argelia', 'code' => 'ALG', 'grupo' => 'J'],
            ['id' => 39, 'name' => 'Austria', 'code' => 'AUT', 'grupo' => 'J'],
            ['id' => 40, 'name' => 'Jordania', 'code' => 'JOR', 'grupo' => 'J'],

            ['id' => 41, 'name' => 'Portugal', 'code' => 'POR', 'grupo' => 'K'],
            ['id' => 42, 'name' => 'Rep. Dem. del Congo', 'code' => 'COD', 'grupo' => 'K'],
            ['id' => 43, 'name' => 'Uzbekistán', 'code' => 'UZB', 'grupo' => 'K'],
            ['id' => 44, 'name' => 'Colombia', 'code' => 'COL', 'grupo' => 'K'],

            ['id' => 45, 'name' => 'Inglaterra', 'code' => 'ENG', 'grupo' => 'L'],
            ['id' => 46, 'name' => 'Croacia', 'code' => 'CRO', 'grupo' => 'L'],
            ['id' => 47, 'name' => 'Ghana', 'code' => 'GHA', 'grupo' => 'L'],
            ['id' => 48, 'name' => 'Panamá', 'code' => 'PAN', 'grupo' => 'L'],
        ];

        foreach ($equipos as &$equipo) {
            $equipo['created_at'] = $now;
            $equipo['updated_at'] = $now;
        }
        unset($equipo);

        DB::table('equipos')->insert($equipos);

        $partidos = $this->partidosFaseGrupos();

        foreach ($partidos as &$partido) {
            $partido['created_at'] = $now;
            $partido['updated_at'] = $now;
        }
        unset($partido);

        foreach (array_chunk($partidos, 25) as $chunk) {
            DB::table('partidos')->insert($chunk);
        }
    }

    /**
     * @return list<array{local_id: int, visitante_id: int, fecha_utc: string, estadio: string, fase: string}>
     */
    private function partidosFaseGrupos(): array
    {
        $f = fn (int $local, int $visitante, string $fechaEt, string $estadio) => [
            'local_id' => $local,
            'visitante_id' => $visitante,
            'fecha_utc' => $this->etToUtc($fechaEt),
            'estadio' => $estadio,
            'fase' => 'Grupos',
            'goles_local' => null,
            'goles_visitante' => null,
        ];

        return [
            // Grupo A
            $f(1, 2, '2026-06-11 15:00', 'Estadio Azteca, Ciudad de México'),
            $f(3, 4, '2026-06-11 22:00', 'Estadio Akron, Zapopan'),
            $f(4, 2, '2026-06-18 12:00', 'Mercedes-Benz Stadium, Atlanta'),
            $f(1, 3, '2026-06-18 21:00', 'Estadio Akron, Zapopan'),
            $f(4, 1, '2026-06-24 21:00', 'Estadio Azteca, Ciudad de México'),
            $f(2, 3, '2026-06-24 21:00', 'Estadio BBVA, Guadalupe'),
            // Grupo B
            $f(5, 6, '2026-06-12 15:00', 'BMO Field, Toronto'),
            $f(7, 8, '2026-06-13 15:00', "Levi's Stadium, Santa Clara"),
            $f(8, 6, '2026-06-18 15:00', 'SoFi Stadium, Inglewood'),
            $f(5, 7, '2026-06-18 18:00', 'BC Place, Vancouver'),
            $f(8, 5, '2026-06-24 15:00', 'BC Place, Vancouver'),
            $f(6, 7, '2026-06-24 15:00', 'Lumen Field, Seattle'),
            // Grupo C
            $f(9, 10, '2026-06-13 18:00', 'MetLife Stadium, East Rutherford'),
            $f(11, 12, '2026-06-13 21:00', 'Gillette Stadium, Foxborough'),
            $f(12, 10, '2026-06-19 18:00', 'Gillette Stadium, Foxborough'),
            $f(9, 11, '2026-06-19 21:00', 'Lincoln Financial Field, Filadelfia'),
            $f(12, 9, '2026-06-24 18:00', 'Hard Rock Stadium, Miami Gardens'),
            $f(10, 11, '2026-06-24 18:00', 'Mercedes-Benz Stadium, Atlanta'),
            // Grupo D
            $f(13, 14, '2026-06-12 21:00', 'SoFi Stadium, Inglewood'),
            $f(15, 16, '2026-06-13 00:00', 'BC Place, Vancouver'),
            $f(16, 14, '2026-06-19 00:00', "Levi's Stadium, Santa Clara"),
            $f(13, 15, '2026-06-19 15:00', 'Lumen Field, Seattle'),
            $f(16, 13, '2026-06-25 22:00', 'SoFi Stadium, Inglewood'),
            $f(14, 15, '2026-06-25 22:00', "Levi's Stadium, Santa Clara"),
            // Grupo E
            $f(17, 18, '2026-06-14 13:00', 'NRG Stadium, Houston'),
            $f(19, 20, '2026-06-14 19:00', 'Lincoln Financial Field, Filadelfia'),
            $f(17, 19, '2026-06-20 16:00', 'BMO Field, Toronto'),
            $f(20, 18, '2026-06-20 20:00', 'Arrowhead Stadium, Kansas City'),
            $f(20, 17, '2026-06-25 16:00', 'MetLife Stadium, East Rutherford'),
            $f(18, 19, '2026-06-25 16:00', 'Lincoln Financial Field, Filadelfia'),
            // Grupo F
            $f(21, 22, '2026-06-14 16:00', 'AT&T Stadium, Arlington'),
            $f(23, 24, '2026-06-14 22:00', 'Estadio BBVA, Guadalupe'),
            $f(21, 23, '2026-06-20 13:00', 'NRG Stadium, Houston'),
            $f(24, 22, '2026-06-20 00:00', 'Estadio BBVA, Guadalupe'),
            $f(24, 21, '2026-06-25 19:00', 'AT&T Stadium, Arlington'),
            $f(22, 23, '2026-06-25 19:00', 'Arrowhead Stadium, Kansas City'),
            // Grupo G
            $f(25, 26, '2026-06-15 15:00', 'Lumen Field, Seattle'),
            $f(27, 28, '2026-06-15 21:00', 'SoFi Stadium, Inglewood'),
            $f(25, 27, '2026-06-21 15:00', 'SoFi Stadium, Inglewood'),
            $f(28, 26, '2026-06-21 21:00', 'BC Place, Vancouver'),
            $f(28, 25, '2026-06-26 23:00', 'BC Place, Vancouver'),
            $f(26, 27, '2026-06-26 23:00', 'Lumen Field, Seattle'),
            // Grupo H
            $f(29, 30, '2026-06-15 12:00', 'Mercedes-Benz Stadium, Atlanta'),
            $f(31, 32, '2026-06-15 18:00', 'Hard Rock Stadium, Miami Gardens'),
            $f(29, 31, '2026-06-21 12:00', 'Mercedes-Benz Stadium, Atlanta'),
            $f(32, 30, '2026-06-21 18:00', 'Hard Rock Stadium, Miami Gardens'),
            $f(32, 29, '2026-06-26 20:00', 'NRG Stadium, Houston'),
            $f(30, 31, '2026-06-26 20:00', 'Estadio Akron, Zapopan'),
            // Grupo I
            $f(33, 34, '2026-06-16 15:00', 'MetLife Stadium, East Rutherford'),
            $f(35, 36, '2026-06-16 18:00', 'Gillette Stadium, Foxborough'),
            $f(33, 35, '2026-06-22 17:00', 'Lincoln Financial Field, Filadelfia'),
            $f(36, 34, '2026-06-22 20:00', 'MetLife Stadium, East Rutherford'),
            $f(36, 33, '2026-06-26 15:00', 'Gillette Stadium, Foxborough'),
            $f(34, 35, '2026-06-26 15:00', 'BMO Field, Toronto'),
            // Grupo J
            $f(37, 38, '2026-06-16 21:00', 'Arrowhead Stadium, Kansas City'),
            $f(39, 40, '2026-06-16 00:00', "Levi's Stadium, Santa Clara"),
            $f(37, 39, '2026-06-22 13:00', 'AT&T Stadium, Arlington'),
            $f(40, 38, '2026-06-22 23:00', "Levi's Stadium, Santa Clara"),
            $f(40, 37, '2026-06-27 22:00', 'AT&T Stadium, Arlington'),
            $f(38, 39, '2026-06-27 22:00', 'Arrowhead Stadium, Kansas City'),
            // Grupo K
            $f(41, 42, '2026-06-17 13:00', 'NRG Stadium, Houston'),
            $f(43, 44, '2026-06-17 22:00', 'Estadio Azteca, Ciudad de México'),
            $f(41, 43, '2026-06-23 13:00', 'NRG Stadium, Houston'),
            $f(44, 42, '2026-06-23 22:00', 'Estadio Akron, Zapopan'),
            $f(44, 41, '2026-06-27 19:30', 'Hard Rock Stadium, Miami Gardens'),
            $f(42, 43, '2026-06-27 19:30', 'Mercedes-Benz Stadium, Atlanta'),
            // Grupo L
            $f(45, 46, '2026-06-17 16:00', 'AT&T Stadium, Arlington'),
            $f(47, 48, '2026-06-17 19:00', 'BMO Field, Toronto'),
            $f(45, 47, '2026-06-23 16:00', 'Gillette Stadium, Foxborough'),
            $f(48, 46, '2026-06-23 19:00', 'BMO Field, Toronto'),
            $f(48, 45, '2026-06-27 17:00', 'MetLife Stadium, East Rutherford'),
            $f(46, 47, '2026-06-27 17:00', 'Lincoln Financial Field, Filadelfia'),
        ];
    }

    /** Horario Eastern Time (EDT, UTC-4) a UTC. */
    private function etToUtc(string $fechaEt): string
    {
        return Carbon::parse($fechaEt, 'America/New_York')
            ->utc()
            ->format('Y-m-d H:i:s');
    }
}
