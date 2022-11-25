<?php

namespace App\Http\Controllers;

use App\Models\Autor;
use App\Models\Llibre;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class LlibreController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    function list() {
        $llibres = Llibre::all();

        return view('llibre.list', ['llibres' => $llibres]);
    }

    function new (Request $request) {
        if ($request->isMethod('post')) {
          $request->validate([
            'titol' => 'required|min:2|max:20',
            'vendes' => 'required',
            'dataP' => 'before:tomorrow',
        ],[
            'required' => 'El camp :attribute es obligatori!',
            'min' => 'El camp :attribute ha de tenir com a mínim :min caràcters!',
            'max' => 'El camp :attribute ha de tenir com a màxim :min caràcters!',
            'before' => 'La data ha de ser com a molt tard, avui'        
        ]);
            $llibre = new Llibre;
            $llibre->titol = $request->titol;
            $llibre->dataP = $request->dataP;
            $llibre->vendes = $request->vendes;
            $llibre->autor_id = $request->autor_id;
            $llibre->save();

            $minutes = 120;
            if ($llibre->autor == null) {
                return redirect()->route('llibre_list', ['autorSelected' => $llibre->autor_id])->with('status', 'Nou llibre ' . $llibre->titol . ' creat i la cookie també!')->withoutCookie('autor_id');
            }
            return redirect()->route('llibre_list', ['autorSelected' => $llibre->autor_id])->with('status', 'Nou llibre ' . $llibre->titol . ' creat i la cookie també!')->cookie(
                'autor_id', $llibre->autor_id, $minutes);
        }
        // si no venim de fer submit al formulari, hem de mostrar el formulari

        $autors = Autor::all();

        return view('llibre.new', ['autors' => $autors]);
    }

    public function edit(Request $request, $id)
    {
        $llibre = Llibre::find($id);
        if ($request->isMethod('post')) {
            // recollim els camps del formulari en un objecte llibre
            $request->validate([
              'titol' => 'required|min:2|max:20',
              'vendes' => 'required',
              'dataP' => 'before:tomorrow',
          ],[
              'required' => 'El camp :attribute es obligatori!',
              'min' => 'El camp :attribute ha de tenir com a mínim :min caràcters!',
              'max' => 'El camp :attribute ha de tenir com a màxim :min caràcters!',
              'before' => 'La data ha de ser com a molt tard, avui'         
          ]);
            $llibre->titol = $request->titol;
            $llibre->dataP = $request->dataP;
            $llibre->vendes = $request->vendes;
            $llibre->autor_id = $request->autor_id;
            $llibre->save();

            return redirect()->route('llibre_list')->with('status', 'Llibre ' . $llibre->titol . ' editat!');
        }
        $autors = Autor::all();
        // si no venim de fer submit al formulari, hem de mostrar el formulari
        return view('llibre.edit', ['llibre' => $llibre], ['autors' => $autors]);
    }

    public function delete($id)
    {
        $llibre = Llibre::find($id);
        $llibre->delete();

        return redirect()->route('llibre_list')->with('status', 'Llibre ' . $llibre->titol . ' eliminat!');
    }
}
