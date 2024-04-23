<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash; // Importa la clase Hash
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{

    public function index()
    {
        return view('admins.index', ['users' => User::get()]);

    }

    public function create()
    {
        return view('admins.create');
    }


    public function store(Request $request)
    {
                // Validar los datos del formulario
                $request->validate([
                    'name' => 'required|string|max:255',
                    'email' => 'required|email|unique:users',
                    'password' => 'required|string|min:6',
                    'role' => 'required|string|max:255',
                ]);

                // Extraer los datos del formulario
                $userData = $request->only(['name', 'email', 'password', 'role']);

                // Encriptar la contraseña
                $userData['password'] = Hash::make($userData['password']);

                // Crear el usuario
                $user = User::create($userData);

                // Redireccionar con mensaje de éxito
                return redirect()->route('user.index')->with('success', 'Usuario creado exitosamente!');


    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return view('admins.show');

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view('admins.edit');

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('user.index')->with('success', 'User Deleted Successfully!!');
    }
}
