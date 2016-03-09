<?php

class ChatLink {
    private $code;
    private $valid;
    private $data;
    private $header;

    public function __construct($code) {
        $this->code = $code;
        $this->valid = false;
        if (self::check($code)) {
            $this->valid = true;
            $this->data = base64_decode(trim($code, '[]&'));
            $header = unpack('Cheader', $this->data);
            $this->header = $header['header'];
        }
    }
    public function isValid() {
        return $this->valid;
    }
    public function getHeader() {
        $types = self::getTypes();
        if (isset($types[$this->header])) {
            return $types[$this->header];
        }
        return $this->header;
    }
    public function getId() {
        $data = unpack('Cheader/Vid', $this->data);
        return $data['id'];
    }
    public function getCoin() {
        $data = unpack('Cheader/Vcoin', $this->data);
        $coin = $data['coin'];
        $gp = floor($coin/10000);
        $sp = floor(($coin - ($gp*10000))/100);
        $cp = $coin - ($sp*100) - ($gp*10000);
        return ['coin' => $coin, 'gp' => $gp, 'sp' => $sp, 'cp' => $cp];
    }
    public function getItemData() {
        $data = $this->data;
        $parsed = unpack('Cheader/Ccount/V*part', $data);
        $itemCount = $parsed['count'];
        if ($parsed['part1'] >= 0xE0000000) {
            return [
                'item_id' => $parsed['part1'] - 0xE0000000,
                'wardrobe_id' => $parsed['part2'],
                'sigils' => [$parsed['part3'], $parsed['part4']]
            ];
        }
        if ($parsed['part1'] >= 0xC0000000) {
            return [
                'item_id' => $parsed['part1'] - 0xC0000000,
                'wardrobe_id' => $parsed['part2'],
                'sigils' => [$parsed['part3']]
            ];
        }
        if ($parsed['part1'] >= 0x80000000) {
            return [
                'item_id' => $parsed['part1'] - 0x80000000,
                'wardrobe_id' => $parsed['part2']
            ];
        }
        if ($parsed['part1'] >= 0x60000000) {
            return [
                'item_id' => $parsed['part1'] - 0x60000000,
                'sigils' => [$parsed['part2'], $parsed['part3']]
            ];
        }
        if ($parsed['part1'] >= 0x40000000) {
            return [
                'item_id' => $parsed['part1'] - 0x40000000,
                'sigils' => [$parsed['part2']]
            ];
        }
        return ['item_id' => $parsed['part1']];
    }

    public static function check($code, $strict = true) {
        if ($strict) {
            if (preg_match('/^\[\&.*\]$/', $code) != 1) {
                return false;
            }
        }
        $result = base64_decode(trim($code, '[]&'));
        if ($result === FALSE) {
            return false;
        }
        return true;
    }

    private static function getTypes() {
        static $types = null;
        if ($types == null) {
            $types = [
                1 => 'Coin',
                2 => 'Item',
                3 => 'NPCText',
                4 => 'MapLink',
                5 => 'PvPGame',
                6 => 'Skill',
                7 => 'Trait',
                8 => 'User',
                9 => 'Recipe',
                10 => 'Wardrobe',
                11 => 'Outfit',
                12 => 'WvWObjective'
            ];
        }
        return $types;
    }
}

/*********/

/*
$link = new ChatLink('[&Bto4AAA=]');
echo ($link->getHeader());
echo ($link->getId());

$link = new ChatLink('[&AdsnAAA=]');
echo ($link->getHeader());
print_r ($link->getCoin());
 */
/*
print_r((new ChatLink('[&AgGqtgAA]'))->getItemData());
print_r((new ChatLink('[&AgGqtgBA/18AAA==]'))->getItemData());
print_r((new ChatLink('[&AgGqtgBg/18AACdgAAA=]'))->getItemData());
print_r((new ChatLink('[&AgGqtgCAfQ4AAA==]'))->getItemData());
print_r((new ChatLink('[&AgGqtgDAfQ4AAP9fAAA=]'))->getItemData());
print_r((new ChatLink('[&AgGqtgDgfQ4AAP9fAAAnYAAA]'))->getItemData());

$link = new ChatLink('azertyuio');
print_r($link);

*/
