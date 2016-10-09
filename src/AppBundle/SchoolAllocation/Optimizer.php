<?php

namespace AppBundle\SchoolAllocation;

class Optimizer
{
    private $startAllocation;
    private $temp;
    private $dt;
    private $startTime;
    private $maxTime;

    /**
     * Optimizer constructor.
     *
     * @param $startAllocation
     * @param $temp
     * @param $dt
     * @param $maxTime in seconds
     */
    public function __construct(Allocation $startAllocation, $temp, $dt, $maxTime)
    {
        $this->startAllocation = $startAllocation;
        $this->temp = $temp; //Set maxTemperature
        $this->dt = $dt;
        $this->maxTime = $maxTime;
        $this->startTime = round(microtime(true) * 1000) / 1000;
    }

    public function optimize()
    {
        $startTime = round(microtime(true) * 1000);
        $bestAllocation = $this->startAllocation;
        $currentAllocation = $bestAllocation;

        while ($this->temp > 0 && (round(microtime(true) * 1000) / 1000 - $this->startTime) < $this->maxTime) {
            //Return the Allocation if score === 100 (Perfect Allocation)
            if ($currentAllocation->evaluate() === 100 || $currentAllocation->isOk()) {
                $currentAllocation->optimizeTime = (round(microtime(true) * 1000) - $startTime) / 1000;

                return $currentAllocation;
            }
            $neighbours = $currentAllocation->generateNeighbours();

            if (sizeof($neighbours) === 0) {
                break;
            }
            //The node in the neighbour list with the highest score
            $pMax = null;
            $bestScore = 0;

            foreach ($neighbours as $neighbour) {
                $currentScore = $neighbour->evaluate();
                if ($currentScore === 100) {
                    $neighbour->optimizeTime = (round(microtime(true) * 1000) - $startTime) / 1000;

                    return $neighbour;
                }

                //Find the best node in the neighbour list
                if ($currentScore > $bestScore) {
                    $bestScore = $currentScore;
                    $pMax = $neighbour;
                    if ($bestScore > $bestAllocation->evaluate()) {
                        $bestAllocation = $neighbour;
                    }
                }
            }

            //$q = [0,1]. 0 when $currentAllocation is as good as $bestAllocation. 1 when $currentAllocation score = 0
            $q = ($bestScore - $currentAllocation->evaluate()) / $currentAllocation->evaluate();

            //The value of $p will be very close to 1 when temperature is high and we are not close to the bestAllocation
            //Larger $p means more likely to explore than exploit
            $p = min(1, exp(-$q / $this->temp));
            $x = rand(0, 100) / 100;

            //q, p and x will make the algorithm search for new random Allocations when we are at a bad node and at early iterations (temperature is still high).
            //The algorithm will search for close Allocations when a good node is found and at later iterations (temperature is low).
            if ($x > $p) {
                $currentAllocation = $pMax; //Exploiting
            } else {
                $currentAllocation = $neighbours[array_rand($neighbours, 1)]; //Exploring
            }
            //Decrease temperature
            $this->temp -= $this->dt;
        }
        //Temperature === 0. The perfect Allocation was not found or does not exists. Return the most optimal Allocation found.
        $bestAllocation->optimizeTime = (round(microtime(true) * 1000) - $startTime) / 1000;

        return $bestAllocation;
    }
}
