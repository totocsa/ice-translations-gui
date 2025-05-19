<?php

namespace Totocsa\TranslationsGUI\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdministratorOrTranslator
{
    public function handle(Request $request, Closure $next): Response
    {
        if (
            !$request->user() ||
            !$request->user()->hasAnyRole(['Administrator', 'Translator'])
        ) {
            //abort(403, 'Access denied');
            return redirect()->route('appRoot');
        }

        return $next($request);
    }
}
