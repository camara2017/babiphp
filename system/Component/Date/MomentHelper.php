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
     * MomentHelper
     * @package BabiPHP\Component\Date
     */
    class MomentHelper
    {
        /**
         * @param $quarter
         * @param $year
         * @param string $timeZoneString
         *
         * @return MomentPeriodVo
         * @throws MomentException
         */
        public static function getQuarterPeriod($quarter, $year, $timeZoneString = 'UTC')
        {
            switch ($quarter)
            {
                case 1:
                    $startMonth = 1;
                    $endMonth = 3;
                    break;
                case 2:
                    $startMonth = 4;
                    $endMonth = 6;
                    break;
                case 3:
                    $startMonth = 7;
                    $endMonth = 9;
                    break;
                case 4:
                    $startMonth = 10;
                    $endMonth = 12;
                    break;
                default:
                    throw new MomentException('Invalid quarter. The range of quarters is 1 - 4. You asked for: ' . $quarter);
            }

            // set start
            $start = new Moment();
            $start
                ->setTimezone($timeZoneString)
                ->setYear($year)
                ->setMonth($startMonth)
                ->setDay(1)
                ->setTime(0, 0, 0);

            // set end
            $end = new Moment();
            $end
                ->setTimezone($timeZoneString)
                ->setYear($year)
                ->setMonth($endMonth)
                ->setDay($end->format('t'))
                ->setTime(23, 59, 59);

            // set period vo
            $momentPeriodVo = new MomentPeriodVo();

            return $momentPeriodVo
                ->setInterval($quarter)
                ->setStartDate($start)
                ->setEndDate($end);
        }
    }