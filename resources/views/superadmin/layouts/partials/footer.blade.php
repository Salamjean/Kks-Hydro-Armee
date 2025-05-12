<footer class="footer">
  <div class="container-fluid"> {{-- Ou juste container si tu veux une largeur limitée --}}
    <div class="d-sm-flex justify-content-center justify-content-sm-between py-2"> {{-- Utilise flexbox pour aligner --}}
      <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">
        Copyright © {{ date('Y') }}.
        <a href="https://kks-technologies.com/" target="_blank">KKS-TECHNOLOGIES</a>.
        Tous droits réservés.
      </span>
      <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center text-muted">
         {{-- Optionnel: Autre texte ou lien --}}
         Fait avec <i class="fas fa-heart text-danger"></i>
      </span>
    </div>
  </div>
</footer>