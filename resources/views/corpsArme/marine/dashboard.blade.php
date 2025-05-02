@extends('corpsArme.layouts.template') {{-- Assurez-vous d'avoir un layout pour les corps --}}

@section('title', 'Tableau de Bord - marine')

@section('content')
<div class="page-heading">
    <h3>Tableau de Bord de la marine</h3>
</div>
<div class="page-content">
    <section class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Bienvenue, {{ Auth::guard('corps')->user()->name }}</h4>
                </div>
                <div class="card-body">
                    <p>Ceci est votre interface spécifique à la marine.</p>
                    {{-- Ajoutez ici le contenu spécifique à la marine --}}
                </div>
            </div>
        </div>
    </section>
</div>
@endsection