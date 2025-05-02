@extends('corpsArme.layouts.template') {{-- Assurez-vous d'avoir un layout pour les corps --}}

@section('title', 'Tableau de Bord - armee de air')

@section('content')
<div class="page-heading">
    <h3>Tableau de Bord de l'armee de l'air</h3>
</div>
<div class="page-content">
    <section class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Bienvenue, {{ Auth::guard('corps')->user()->name }}</h4>
                </div>
                <div class="card-body">
                    <p>Ceci est votre interface spécifique à armee de l'air.</p>
                    {{-- Ajoutez ici le contenu spécifique à armee de l'air --}}
                </div>
            </div>
        </div>
    </section>
</div>
@endsection