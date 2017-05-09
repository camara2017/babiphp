<?php
/**
* BabiPHP : The Simple and Fast Development Framework (http://babiphp.org)
* Copyright (c) BabiPHP. (http://babiphp.org)
*
* Licensed under The GNU General Public License
* For full copyright and license information, please see the LICENSE.txt
* Redistributions of files must retain the above copyright notice.
*
* @copyright     Copyright (c) BabiPHP. (http://babiphp.org)
* @link          http://babiphp.org BabiPHP Project
* @since         BabiPHP v 0.8.8
* @license       http://www.gnu.org/licenses/ GNU License
*
* 
* Not edit this file
*
*/

    namespace BabiPHP\Component\Date;

    /**
     * MomentPeriodVo
     * @package BabiPHP\Component\Date
     */
    class MomentPeriodVo
    {
        /** @var  Moment */
        protected $startDate;

        /** @var  Moment */
        protected $endDate;

        /** @var  Moment */
        protected $refDate;

        /** @var  int */
        protected $interval;

        /**
         * @return int
         */
        public function getInterval()
        {
            return $this->interval;
        }

        /**
         * @param int $interval
         *
         * @return MomentPeriodVo
         */
        public function setInterval($interval)
        {
            $this->interval = $interval;

            return $this;
        }

        /**
         * @param Moment $reference
         *
         * @return MomentPeriodVo
         */
        public function setRefDate(Moment $reference)
        {
            $this->refDate = $reference;

            return $this;
        }

        /**
         * @return \Moment\Moment
         */
        public function getRefDate()
        {
            return $this->refDate;
        }

        /**
         * @param Moment $end
         *
         * @return MomentPeriodVo
         */
        public function setEndDate(Moment $end)
        {
            $this->endDate = $end;

            return $this;
        }

        /**
         * @return Moment
         */
        public function getEndDate()
        {
            return $this->endDate;
        }

        /**
         * @param Moment $start
         *
         * @return MomentPeriodVo
         */
        public function setStartDate(Moment $start)
        {
            $this->startDate = $start;

            return $this;
        }

        /**
         * @return Moment
         */
        public function getStartDate()
        {
            return $this->startDate;
        }
    }