<?php

    namespace App\Imports;

    use App\Models\Payment;
    use Illuminate\Support\Collection;
    use Maatwebsite\Excel\Concerns\ToCollection;
    use Illuminate\Support\Facades\Hash;
    use Maatwebsite\Excel\Concerns\ToModel;
    use Maatwebsite\Excel\Concerns\WithHeadingRow;
    use Maatwebsite\Excel\Concerns\WithStartRow;

    class PaymentImport implements ToModel, WithStartRow{
        public function startRow(): int{
            return 8;
        }

        public function model(array $row){
            if($row[0] != '' || $row[0] != null){
                return new Payment([
                    'party_name' => $row[0],
                    'bill_no' => $row[1],
                    'bill_date' => $row[2],
                    'due_days' => $row[3],
                    'bill_amount' => $row[4],
                    'balance_amount' => $row[5],
                    'mobile_no' => $row[6]
                ]);
            }
        }
    }
