@extends('layouts.app')


@section('template_title')

@endsection


@section('template_css')

    <style>
        .my-button {
            background-color: #4CAF50;
            border: none;
            color: white;
            padding: 15px 32px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
        }

        .espaciado-sup {
            margin-top: 30px !important;
        }
    </style>

@endsection




@section('template_text')


    <tr>
        <td bgcolor="#ffffff" align="center" valign="top" style="padding: 5px 20px 5px 20px; border-radius: 4px 4px 0px 0px; color: #111111; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 48px; font-weight: 400; letter-spacing: 1px; line-height: 48px;">
            <h1 style="font-size: 24px; font-weight: 400;">¡Hola!</h1>
        </td>
    </tr>

    <tr>
        <td bgcolor="#ffffff" align="left" style="padding: 20px 30px 40px 30px; color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 400; line-height: 25px;">
            <p style="margin: 0;">El Empleado: <b>{{ $fullName }}</b> ha descargado un archivo de Excel que contiene la lista de todos los productos.</p>
        </td>
    </tr>

@endsection

