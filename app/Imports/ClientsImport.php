<?php

namespace App\Imports;

use App\IntranetCliente;
use App\IntranetContacto;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Carbon\Carbon;

class ClientsImport implements ToModel
{
    /**
     * @param array $row
     *
     * @return IntranetCliente|null
     */
    public function model(array $row)
    {
        $cliente =  IntranetCliente::create([
            'nombre'=>$row[0],
            'id_tipo'=>$row[1] == "EMPRESAS"? 2 : 1,
            'fecha_registro'=>Carbon::now(),
            'nombre_simi'=>$row[0],
            'id_usuario'=> 1,
            'ruc'=>$row[1] == "EMPRESAS"? $row[2] : null,
            'dni'=>$row[1] == "EMPRESAS"? null : $row[2],
        ]);
        $data =  IntranetCliente::create([
            'id_cliente'=>$cliente->id,
            'tipo'=>'Telefono',
            'pertenece'=>$row[1] == "EMPRESAS"? "Empresa":"Persona",
            'descripcion'=>$row[3],
            'observacion'=>"ninguna",
        ]);
        return $data;
    }
}
