<?php

namespace App\Support\WorldCup;

final class Bracket2026
{
    public const FASE_R32 = 'Dieciseisavos';
    public const FASE_R16 = 'Octavos';
    public const FASE_QF = 'Cuartos';
    public const FASE_SF = 'Semifinal';
    public const FASE_3RD = 'Tercer puesto';
    public const FASE_FINAL = 'Final';

    public static function slots(): array
    {
        return [
            '73' => self::slot(self::FASE_R32, self::g2('A'), self::g2('B')),
            '74' => self::slot(self::FASE_R32, self::g1('E'), self::th(['A', 'B', 'C', 'D', 'F'])),
            '75' => self::slot(self::FASE_R32, self::g1('F'), self::g2('C')),
            '76' => self::slot(self::FASE_R32, self::g1('C'), self::g2('F')),
            '77' => self::slot(self::FASE_R32, self::g1('I'), self::th(['C', 'D', 'F', 'G', 'H'])),
            '78' => self::slot(self::FASE_R32, self::g2('E'), self::g2('I')),
            '79' => self::slot(self::FASE_R32, self::g1('A'), self::th(['C', 'E', 'F', 'H', 'I'])),
            '80' => self::slot(self::FASE_R32, self::g1('L'), self::th(['E', 'H', 'I', 'J', 'K'])),
            '81' => self::slot(self::FASE_R32, self::g1('D'), self::th(['B', 'E', 'F', 'I', 'J'])),
            '82' => self::slot(self::FASE_R32, self::g1('G'), self::th(['A', 'E', 'H', 'I', 'J'])),
            '83' => self::slot(self::FASE_R32, self::g2('K'), self::g2('L')),
            '84' => self::slot(self::FASE_R32, self::g1('H'), self::g2('J')),
            '85' => self::slot(self::FASE_R32, self::g1('B'), self::th(['E', 'F', 'G', 'I', 'J'])),
            '86' => self::slot(self::FASE_R32, self::g1('J'), self::g2('H')),
            '87' => self::slot(self::FASE_R32, self::g1('K'), self::th(['D', 'E', 'I', 'J', 'L'])),
            '88' => self::slot(self::FASE_R32, self::g2('D'), self::g2('G')),
            '89' => self::slot(self::FASE_R16, self::wn('74'), self::wn('77')),
            '90' => self::slot(self::FASE_R16, self::wn('73'), self::wn('75')),
            '91' => self::slot(self::FASE_R16, self::wn('76'), self::wn('78')),
            '92' => self::slot(self::FASE_R16, self::wn('79'), self::wn('80')),
            '93' => self::slot(self::FASE_R16, self::wn('83'), self::wn('84')),
            '94' => self::slot(self::FASE_R16, self::wn('81'), self::wn('82')),
            '95' => self::slot(self::FASE_R16, self::wn('86'), self::wn('88')),
            '96' => self::slot(self::FASE_R16, self::wn('85'), self::wn('87')),
            '97' => self::slot(self::FASE_QF, self::wn('89'), self::wn('90')),
            '98' => self::slot(self::FASE_QF, self::wn('93'), self::wn('94')),
            '99' => self::slot(self::FASE_QF, self::wn('91'), self::wn('92')),
            '100' => self::slot(self::FASE_QF, self::wn('95'), self::wn('96')),
            '101' => self::slot(self::FASE_SF, self::wn('97'), self::wn('98')),
            '102' => self::slot(self::FASE_SF, self::wn('99'), self::wn('100')),
            '103' => self::slot(self::FASE_3RD, self::ls('101'), self::ls('102')),
            '104' => self::slot(self::FASE_FINAL, self::wn('101'), self::wn('102')),
        ];
    }

    public static function layout(): array
    {
        return [
            'left' => [
                ['fase' => self::FASE_R32, 'slots' => ['74', '77', '73', '75', '83', '84', '81', '82']],
                ['fase' => self::FASE_R16, 'slots' => ['89', '90', '93', '94']],
                ['fase' => self::FASE_QF, 'slots' => ['97', '98']],
                ['fase' => self::FASE_SF, 'slots' => ['101']],
            ],
            'right' => [
                ['fase' => self::FASE_R32, 'slots' => ['76', '78', '79', '80', '86', '88', '85', '87']],
                ['fase' => self::FASE_R16, 'slots' => ['91', '92', '95', '96']],
                ['fase' => self::FASE_QF, 'slots' => ['99', '100']],
                ['fase' => self::FASE_SF, 'slots' => ['102']],
            ],
            'final' => '104',
            'third' => '103',
        ];
    }

    private static function slot(string $fase, array $home, array $away): array
    {
        return ['fase' => $fase, 'home' => $home, 'away' => $away];
    }

    private static function g1(string $grupo): array
    {
        return ['type' => 'group', 'grupo' => $grupo, 'pos' => 1];
    }

    private static function g2(string $grupo): array
    {
        return ['type' => 'group', 'grupo' => $grupo, 'pos' => 2];
    }

    private static function th(array $grupos): array
    {
        return ['type' => 'thirds', 'grupos' => $grupos];
    }

    private static function wn(string $slot): array
    {
        return ['type' => 'winner', 'slot' => $slot];
    }

    private static function ls(string $slot): array
    {
        return ['type' => 'loser', 'slot' => $slot];
    }
}
