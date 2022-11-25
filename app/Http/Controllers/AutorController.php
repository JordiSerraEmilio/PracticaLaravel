<?php
namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Models\Autor;
use App\Models\Llibre;
use Illuminate\Support\Facades\Storage;

class AutorController extends BaseController{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    function list() 
    { 
      $autors = Autor::all();

      return view('autor.list', ['autors' => $autors]);
    }

    function new(Request $request) 
    { 
      if ($request->isMethod('post')) {    
        $request->validate([
          'nom' => 'required|max:20',
          'cognoms' => 'required|max:30'
      ],[
        'required' => 'El camp :attribute es obligatori!',
        'min' => 'El camp :attribute ha de tenir com a mínim :min caràcters!',
        'max' => 'El camp :attribute ha de tenir com a màxim :min caràcters!',
      ]);
        $autor = new Autor;
        $autor->nom = $request->nom;
        $autor->cognoms = $request->cognoms;
        if ($request->file('imatge')) {
          $file = $request->file('imatge');
          $filename = $request->nom . '_' . $request->cognoms . '.' . $file->getClientOriginalExtension();
          $file->move(public_path(env('RUTA_IMATGES', false)), $filename);
          $autor->imatge = $filename;
      }
        $autor->save();

        return redirect()->route('autor_list')->with('status', 'Nou autor '.$autor->nom.' creat!');
      }
      // si no venim de fer submit al formulari, hem de mostrar el formulari

      $autors = Autor::all();

      return view('autor.new', ['autors' => $autors]);
    }

    function edit(Request $request, $id) 
    { 
      $autor = Autor::find($id);
      if ($request->isMethod('post')) {    
        $request->validate([
          'nom' => 'required|max:20',
          'cognoms' => 'required|max:30'
      ],[
        'required' => 'El camp :attribute es obligatori!',
        'min' => 'El camp :attribute ha de tenir com a mínim :min caràcters!',
        'max' => 'El camp :attribute ha de tenir com a màxim :min caràcters!',
      ]);
        $autor->nom = $request->nom;
        $autor->cognoms = $request->cognoms;
        if ($request->file('imatge')) {
          $file = $request->file('imatge');
          $filename = $request->nom . '_' . $request->cognoms . '.' . $file->getClientOriginalExtension();
          $file->move(public_path(env('RUTA_IMATGES', false)), $filename);
          $autor->imatge = $filename;                
      }
      if (isset($request->deleteImage)) {                
          Storage::delete(env('RUTA_IMATGES', false) . $autor->imatge);
          $autor->imatge = null;
      }
        $autor->save();

        return redirect()->route('autor_list')->with('status', 'Autor '.$autor->nom.' editat!');
      }
      // si no venim de fer submit al formulari, hem de mostrar el formulari
      return view('autor.edit', ['autor' => $autor]);
    }

    function delete($id) 
    { 
      $autor = Autor::find($id);
      $llibres = LLibre::all();
      foreach($llibres as $llibre){
        if($llibre->autor_id == $id){
            $llibre->delete();
        }
      }
      $autor->delete();

      return redirect()->route('autor_list')->with('status', 'Autor '.$autor->nom.' eliminat!');
    }
}


