<?php

namespace AppBundle\Services;

use AppBundle\Entity\Claim;
use Doctrine\ORM\Query\Expr;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class Util
{

    private $result = [];
    private $a = [];
    private $k;
    private $n;

    private function printResult() { // hàm dùng để in một cấu hình ra ngoài
        return $this->a;
    }

    private function backtrack($i)
    { // hàm quay lui
        for ($j = $this->a[$i - 1] + 1; $j <= $this->n - $this->k + $i; $j++) { // xét các khả năng của j
            $this->a[$i] = $j; // ghi nhận một giá trị của j
            if ($i == $this->k) { // nếu cấu hình đã đủ k phần tử
                // in một cấu hình ra ngoài
                $this->result[] = $this->a;
            } else {
                $this->backtrack($i + 1); // quay lui
            }

        }

    }
    private function main($arr)
    {
        $this->n = count($arr);
        for ($this->k = 1; $this->k < $this->n; $this->k++) {
            $this->a[0] = 1; // khởi tạo giá trị a[0]
            $this->backtrack(1);
        }
        return $this->result;
    }
    public function getResult($arr){
        $this->main($arr);
        $result = [];
        foreach ($this->result as $data){
            $temp =[];
            foreach ($data as $item){
                $temp[] = $arr[$item-1];
            }
            $result[] = implode('>',$temp);
        }
        return $result;
    }

}
