<?php

namespace App\Services;

class MultipleLinearRegressionService
{
    // data: array of ['x1'=>..., 'x2'=>..., 'y'=>...]
    public function fit(array $data): array
    {
        $n = count($data);
        if ($n < 3) {
            throw new \InvalidArgumentException("Butuh minimal 3 observasi.");
        }

        $X = [];
        $Y = [];
        foreach ($data as $row) {
            $X[] = [1.0, (float)$row['x1'], (float)$row['x2']];
            $Y[] = [(float)$row['y']];
        }

        $Xt = $this->transpose($X);
        $XtX = $this->matMul($Xt, $X); // 3x3
        $XtY = $this->matMul($Xt, $Y); // 3x1

        $inv = $this->invertMatrix($XtX);
        $beta = $this->matMul($inv, $XtY);

        return [
            'a'  => $beta[0][0],
            'b1' => $beta[1][0],
            'b2' => $beta[2][0],
        ];
    }

    public function predict(array $coeffs, float $x1, float $x2): float
    {
        return $coeffs['a'] + $coeffs['b1'] * $x1 + $coeffs['b2'] * $x2;
    }

    public function evaluate(array $actuals, array $preds): array
    {
        $n = count($actuals);
        if ($n === 0 || $n !== count($preds)) {
            throw new \InvalidArgumentException('Actuals dan preds harus sama panjang & tidak kosong.');
        }
        $sumAbs = 0.0;
        $sumSq = 0.0;
        $sumPerc = 0.0;
        $countNonZero = 0;
        for ($i = 0; $i < $n; $i++) {
            $a = (float)$actuals[$i];
            $p = (float)$preds[$i];
            $err = $a - $p;
            $abs = abs($err);
            $sumAbs += $abs;
            $sumSq += $err * $err;
            if ($a != 0.0) {
                $sumPerc += ($abs / abs($a));
                $countNonZero++;
            }
        }
        $MAD = $sumAbs / $n;
        $MSE = $sumSq / $n;
        $MAPE = $countNonZero ? ($sumPerc / $countNonZero) * 100.0 : null;

        return ['MAD' => $MAD, 'MSE' => $MSE, 'MAPE' => $MAPE];
    }

    /* --- helper matrix ops --- */
    private function transpose(array $m): array
    {
        $r = [];
        $rows = count($m);
        $cols = count($m[0]);
        for ($j = 0; $j < $cols; $j++) {
            $r[$j] = [];
            for ($i = 0; $i < $rows; $i++) $r[$j][$i] = $m[$i][$j];
        }
        return $r;
    }

    private function matMul(array $A, array $B): array
    {
        $Arows = count($A);
        $Acols = count($A[0]);
        $Brows = count($B);
        $Bcols = count($B[0]);
        if ($Acols !== $Brows) throw new \InvalidArgumentException('Ukuran matriks tidak cocok.');
        $R = [];
        for ($i = 0; $i < $Arows; $i++) {
            $R[$i] = array_fill(0, $Bcols, 0.0);
            for ($j = 0; $j < $Bcols; $j++) {
                $sum = 0.0;
                for ($k = 0; $k < $Acols; $k++) {
                    $sum += $A[$i][$k] * $B[$k][$j];
                }
                $R[$i][$j] = $sum;
            }
        }
        return $R;
    }

    private function invertMatrix(array $M): array
    {
        $n = count($M);
        for ($i = 0; $i < $n; $i++) if (count($M[$i]) !== $n) throw new \InvalidArgumentException('Matrix harus NxN.');
        $aug = [];
        for ($i = 0; $i < $n; $i++) {
            $aug[$i] = array_merge($M[$i], array_fill(0, $n, 0.0));
            $aug[$i][$n + $i] = 1.0;
        }

        for ($col = 0; $col < $n; $col++) {
            $pivot = $col;
            for ($r = $col; $r < $n; $r++) {
                if (abs($aug[$r][$col]) > abs($aug[$pivot][$col])) $pivot = $r;
            }
            if (abs($aug[$pivot][$col]) < 1e-12) {
                throw new \RuntimeException('Matrix XtX singular (tidak bisa invert). Cek multikolinearitas / variasi data.');
            }
            if ($pivot !== $col) {
                $tmp = $aug[$col];
                $aug[$col] = $aug[$pivot];
                $aug[$pivot] = $tmp;
            }
            $pv = $aug[$col][$col];
            for ($j = 0; $j < 2 * $n; $j++) $aug[$col][$j] /= $pv;
            for ($r = 0; $r < $n; $r++) {
                if ($r == $col) continue;
                $factor = $aug[$r][$col];
                if ($factor == 0.0) continue;
                for ($j = 0; $j < 2 * $n; $j++) $aug[$r][$j] -= $factor * $aug[$col][$j];
            }
        }
        $inv = [];
        for ($i = 0; $i < $n; $i++) $inv[$i] = array_slice($aug[$i], $n, $n);
        return $inv;
    }
}
