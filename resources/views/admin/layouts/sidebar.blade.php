<div id="sidebar" class="active">
  <div class="sidebar-wrapper active">
      <div class="sidebar-header">
          <div class="d-flex justify-content-between">
              <div class="logo">
                  <a href="{{ route('admin.dashboard') }}"><img src="assets/images/logo/logo.png" alt="Logo" srcset=""></a>
              </div>
              <div class="toggler">
                  <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
              </div>
          </div>
      </div>
      <div class="sidebar-menu">
          <ul class="menu">
              <li class="sidebar-title">Menu</li>

              <li class="sidebar-item active ">
                  <a href="{{ route('admin.dashboard') }}" class='sidebar-link'>
                      <i class="bi bi-grid-fill"></i>
                      <span>Tableau de bord</span>
                  </a>
              </li>

              <li class="sidebar-item  has-sub">
                  <a href="#" class='sidebar-link'>
                      <i class="bi bi-stack"></i>
                      <span>Corps d'armée</span>
                  </a>
                  <ul class="submenu ">
                      <li class="submenu-item ">
                          <a href="{{ route('admin.create.army') }}">Ajout d'un corps</a>
                      </li>
                      <li class="submenu-item ">
                          <a href="{{ route('admin.army') }}">Listes des corps</a>
                      </li>
                  </ul>
              </li>

              <li class="sidebar-item  has-sub">
                  <a href="#" class='sidebar-link'>
                      <i class="bi bi-collection-fill"></i>
                      <span>Extra Components</span>
                  </a>
                  <ul class="submenu ">
                      <li class="submenu-item ">
                          <a href="extra-component-avatar.html">Avatar</a>
                      </li>
                      <li class="submenu-item ">
                          <a href="extra-component-sweetalert.html">Sweet Alert</a>
                      </li>
                  </ul>
              </li>

              

              <li class="sidebar-title">Forms &amp; Tables</li>

              <li class="sidebar-item  has-sub">
                  <a href="#" class='sidebar-link'>
                      <i class="bi bi-hexagon-fill"></i>
                      <span>Form Elements</span>
                  </a>
                  <ul class="submenu ">
                      <li class="submenu-item ">
                          <a href="form-element-input.html">Input</a>
                      </li>
                      <li class="submenu-item ">
                          <a href="form-element-input-group.html">Input Group</a>
                      </li>
                      <li class="submenu-item ">
                          <a href="form-element-select.html">Select</a>
                      </li>
                      <li class="submenu-item ">
                          <a href="form-element-radio.html">Radio</a>
                      </li>
                      <li class="submenu-item ">
                          <a href="form-element-checkbox.html">Checkbox</a>
                      </li>
                      <li class="submenu-item ">
                          <a href="form-element-textarea.html">Textarea</a>
                      </li>
                  </ul>
              </li>

              <li class="sidebar-item  ">
                  <a href="form-layout.html" class='sidebar-link'>
                      <i class="bi bi-file-earmark-medical-fill"></i>
                      <span>Form Layout</span>
                  </a>
              </li>
          </ul>
      </div>
      <button class="sidebar-toggler btn x"><i data-feather="x"></i></button>
  </div>
</div>