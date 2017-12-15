<?php

namespace pocketmine\block;

class RedstoneLampActive extends RedstoneLamp{
    
    protected $id = self::REDSTONE_LAMP_ACTIVE;
    
    public function getLightLevel(){
        return 10;
    }

    public function turnOn()
    {
        $this->meta = 0;
        $this->getLevel()->setBlock($this, $this, true, false);
        return true;
    }

    public function turnOff()
    {
        $this->getLevel()->setBlock($this, new RedstoneLamp(), true, true);
        return true;
    }
}
