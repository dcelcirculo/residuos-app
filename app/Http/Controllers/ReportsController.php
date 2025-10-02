<?php

namespace App\Http\Controllers;

use App\Models\Recoleccion;
use App\Models\User;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function userReport(Request $request)
    {
        $this->ensureAdmin();

        $users = User::orderBy('name')->get(['id','name','email']);

        $selectedUserId = $request->integer('user_id');
        $from = $request->date('from');
        $to = $request->date('to');

        $records = collect();
        $totals = ['kilos' => 0, 'puntos' => 0];

        if ($selectedUserId) {
            $query = Recoleccion::with(['solicitud'])
                ->whereHas('solicitud', function ($q) use ($selectedUserId) {
                    $q->where('user_id', $selectedUserId);
                });

            if ($from) {
                $query->whereDate('fecha_real', '>=', $from);
            }
            if ($to) {
                $query->whereDate('fecha_real', '<=', $to);
            }

            $records = $query->orderByDesc('fecha_real')->get();

            $totals['kilos'] = $records->sum('kilos');
            $totals['puntos'] = $records->sum('puntos');
        }

        return view('reports.user', compact('users', 'selectedUserId', 'records', 'totals', 'from', 'to'));
    }

    public function usersSummary(Request $request)
    {
        $this->ensureAdmin();

        $localidades = User::whereNotNull('localidad')
            ->distinct()
            ->orderBy('localidad')
            ->pluck('localidad');

        $localidad = $request->string('localidad')->trim();
        $localidad = $localidad->isNotEmpty() ? $localidad->value() : null;
        $from = $request->date('from');
        $to = $request->date('to');

        $query = Recoleccion::query()
            ->selectRaw('solicitudes.tipo_residuo, COUNT(*) as total_recolecciones, SUM(recolecciones.kilos) as kilos, SUM(recolecciones.puntos) as puntos')
            ->join('solicitudes', 'solicitudes.id', '=', 'recolecciones.solicitud_id')
            ->join('users', 'users.id', '=', 'solicitudes.user_id')
            ->groupBy('solicitudes.tipo_residuo');

        if ($localidad !== null) {
            $query->where('users.localidad', $localidad);
        }
        if ($from) {
            $query->whereDate('recolecciones.fecha_real', '>=', $from);
        }
        if ($to) {
            $query->whereDate('recolecciones.fecha_real', '<=', $to);
        }

        $summary = $query->orderBy('solicitudes.tipo_residuo')->get();

        return view('reports.users', compact('summary', 'localidades', 'localidad', 'from', 'to'));
    }

    public function companyReport(Request $request)
    {
        $this->ensureAdmin();

        $companies = User::where('role', 'empresa')
            ->with('empresaRecolectora')
            ->orderBy('name')
            ->get(['id','name']);

        $companyId = $request->integer('company_id') ?: null;
        $tipoResiduo = $request->filled('tipo_residuo')
            ? trim((string) $request->input('tipo_residuo'))
            : null;
        $from = $request->date('from');
        $to = $request->date('to');

        $records = collect();
        $totals = ['kilos' => 0, 'puntos' => 0];

        if ($companyId) {
            $query = Recoleccion::with(['solicitud.user'])
                ->where('user_id', $companyId);

            if ($tipoResiduo) {
                $query->whereHas('solicitud', function ($q) use ($tipoResiduo) {
                    $q->where('tipo_residuo', $tipoResiduo);
                });
            }
            if ($from) {
                $query->whereDate('fecha_real', '>=', $from);
            }
            if ($to) {
                $query->whereDate('fecha_real', '<=', $to);
            }

            $records = $query->orderByDesc('fecha_real')->get();

            $totals['kilos'] = $records->sum('kilos');
            $totals['puntos'] = $records->sum('puntos');
        }

        $selectedCompany = $companyId ? $companies->firstWhere('id', $companyId) : null;
        $especialidades = $selectedCompany?->empresaRecolectora?->especialidades ?? [];

        return view('reports.company', compact('companies', 'companyId', 'records', 'totals', 'from', 'to', 'tipoResiduo', 'especialidades'));
    }

    private function ensureAdmin(): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
    }
}
