<?php $breadcrumbs = array_filter(explode("/", $_SERVER['REQUEST_URI'])); ?>
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
 <div class="app-brand demo ">
    <a href="/admission/applications" class="app-brand-link">
      <div class="d-flex align-items-center">
        <div class="flex-shrink-0 me-2">
          <div class="avatar avatar-online">
            <img src="..<?= $_SESSION['Photo'] ?>" alt="<?= $_SESSION['Name'] ?>" data-src-retina="<?= $_SESSION['Photo'] ?>" class="rounded-circle">
          </div>
        </div>

        <div class="flex-grow-1">
          <span class="fw-medium d-block small ms-3 fs-6 text-black fw-bold"><?= ucwords(strtolower($_SESSION['Name'])) ?></span>
        </div>
      </div>
    </a>

    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M8.47365 11.7183C8.11707 12.0749 8.11707 12.6531 8.47365 13.0097L12.071 16.607C12.4615 16.9975 12.4615 17.6305 12.071 18.021C11.6805 18.4115 11.0475 18.4115 10.657 18.021L5.83009 13.1941C5.37164 12.7356 5.37164 11.9924 5.83009 11.5339L10.657 6.707C11.0475 6.31653 11.6805 6.31653 12.071 6.707C12.4615 7.09747 12.4615 7.73053 12.071 8.121L8.47365 11.7183Z" fill-opacity="0.9" />
        <path d="M14.3584 11.8336C14.0654 12.1266 14.0654 12.6014 14.3584 12.8944L18.071 16.607C18.4615 16.9975 18.4615 17.6305 18.071 18.021C17.6805 18.4115 17.0475 18.4115 16.657 18.021L11.6819 13.0459C11.3053 12.6693 11.3053 12.0587 11.6819 11.6821L16.657 6.707C17.0475 6.31653 17.6805 6.31653 18.071 6.707C18.4615 7.09747 18.4615 7.73053 18.071 8.121L14.3584 11.8336Z" fill-opacity="0.4" />
      </svg>
    </a>
  </div>

<div class="menu-inner-shadow"></div>
<ul class="menu-inner py-1">
  <li class="menu-item">
    <a href="/dashboards/dashboards" class="menu-link ">
      <i class="menu-icon tf-icons ri-home-4-fill"></i>
      <div data-i18n="Dashboards">Dashboards</div>
    </a>
  </li>
  <li class="menu-item">
    <a href="#" class="menu-link menu-toggle">
      <i class="menu-icon tf-icons ri-table-alt-line"></i>
      <div data-i18n="Admissions">Admissions</div>
    </a>
    <ul class="menu-sub">
      <li class="menu-item active">
        <a href="/admission/application-form" class="menu-link">
          <div data-i18n="Apply Fresh">Apply Fresh</div>
        </a>
      </li>
      <li class="menu-item">
        <a href="/admission/applications" class="menu-link">
          <div data-i18n="Applications">Applications</div>
        </a>
      </li>
    </ul>
  </li>
</ul>
</aside>
