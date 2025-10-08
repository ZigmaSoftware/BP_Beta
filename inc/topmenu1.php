<!-- 📱 MOBILE-ONLY NAVBAR (Bootstrap 5 Offcanvas) -->
<div class="navbar-custom border-bottom d-lg-none">
  <nav class="navbar topbar container-fluid p-0 ps-1 position-relative navbar-light bg-white">

    <!-- Brand (left) -->
    <a href="" class="navbar-brand d-flex align-items-center py-2">
      <img src="assets/images/logo.png" alt="logo" style="height:36px;">
    </a>

    <!-- Hamburger (right) -->
    <button class="navbar-toggler p-2 me-1" type="button"
            data-bs-toggle="offcanvas" data-bs-target="#mobileNav"
            aria-controls="mobileNav" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Offcanvas Menu -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="mobileNav" aria-labelledby="mobileNavLabel">
      <div class="offcanvas-header">
        <h5 class="offcanvas-title d-flex align-items-center gap-2" id="mobileNavLabel">
          <img src="assets/images/logo.png" alt="logo" style="height:28px;">
          Menu
        </h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>

      <div class="offcanvas-body d-flex flex-column">
        <!-- 🔽 Main Menu as Accordion -->
        <div class="accordion" id="mobileMegaAccordion">
          <?php
            $current_file  = isset($_GET['file']) ? $_GET['file'] : '';
            $main_screens  = main_screen();
            foreach ($main_screens as $main_key => $main_value) {
              if (in_array($main_value['unique_id'], $_SESSION['main_screens'])) {
                $menu_main_name = disname($main_value["screen_main_name"]);
                $menu_main_icon = $main_value["icon_name"] ? '<i class="mdi ' . $main_value["icon_name"] . '"></i>' : '';

                // detect active
                $is_main_menu_active = false;
                $screen_sections = section_name('', $main_value['unique_id']);
                foreach ($screen_sections as $section_key => $section_value) {
                  if (in_array($section_value['unique_id'], $_SESSION['sections'])) {
                    $user_screens = user_screen('', $section_value['unique_id']);
                    foreach ($user_screens as $screens_key => $screens_value) {
                      if (in_array($screens_value['unique_id'], $_SESSION['screens'])) {
                        $menu_screen_folder = $screens_value["folder_name"];
                        $menu_url = $menu_screen_folder . "/list";
                        if ($current_file === $menu_url) { $is_main_menu_active = true; break 2; }
                      }
                    }
                  }
                }

                $accId      = 'acc_' . $main_value['unique_id'];
                $headingId  = 'hd_'  . $main_value['unique_id'];
                $collapseId = 'col_' . $main_value['unique_id'];
          ?>
          <div class="accordion-item">
            <h2 class="accordion-header" id="<?php echo $headingId; ?>">
              <button class="accordion-button <?php echo $is_main_menu_active ? '' : 'collapsed'; ?>" type="button"
                      data-bs-toggle="collapse" data-bs-target="#<?php echo $collapseId; ?>"
                      aria-expanded="<?php echo $is_main_menu_active ? 'true' : 'false'; ?>"
                      aria-controls="<?php echo $collapseId; ?>">
                <span class="<?php echo $is_main_menu_active ? 'text-primary fw-semibold' : ''; ?>">
                  <?php echo $menu_main_icon . ' ' . $menu_main_name; ?>
                </span>
              </button>
            </h2>
            <div id="<?php echo $collapseId; ?>" class="accordion-collapse collapse <?php echo $is_main_menu_active ? 'show' : ''; ?>" aria-labelledby="<?php echo $headingId; ?>" data-bs-parent="#mobileMegaAccordion">
              <div class="accordion-body pt-2 pb-3">

                <?php
                  $screen_sections = section_name('', $main_value['unique_id']);
                  foreach ($screen_sections as $section_key => $section_value) {
                    if (in_array($section_value['unique_id'], $_SESSION['sections'])) {
                      $menu_section_name = disname($section_value["section_name"]);
                ?>
                <div class="mb-3">
                  <div class="small text-uppercase text-muted mb-1"><?php echo $menu_section_name; ?></div>
                  <ul class="list-group list-group-flush">
                    <?php
                      $user_screens = user_screen('', $section_value['unique_id']);
                      foreach ($user_screens as $screens_key => $screens_value) {
                        if (in_array($screens_value['unique_id'], $_SESSION['screens'])) {
                          $menu_screen_name   = disname($screens_value["screen_name"]);
                          $menu_screen_folder = $screens_value["folder_name"];
                          $menu_url           = $menu_screen_folder . "/list";
                          $is_active          = ($current_file === $menu_url);
                    ?>
                    <li class="list-group-item px-0 py-2 border-0">
                      <a href="index.php?file=<?php echo $menu_url; ?>"
                         class="d-flex align-items-center justify-content-between link-body-emphasis <?php echo $is_active ? 'text-primary fw-semibold' : ''; ?>"
                         aria-current="<?php echo $is_active ? 'page' : ''; ?>">
                        <span><?php echo $menu_screen_name; ?></span>
                        <?php if ($is_active) { ?><i class="mdi mdi-chevron-right"></i><?php } ?>
                      </a>
                    </li>
                    <?php } } ?>
                  </ul>
                </div>
                <?php } } ?>

              </div>
            </div>
          </div>
          <?php } } ?>
        </div>

        <!-- Spacer -->
        <div class="flex-grow-1"></div>

        <!-- 👤 Profile (sticky at bottom inside offcanvas) -->
        <div class="border-top pt-3">
          <div class="d-flex align-items-center mb-2">
            <img style="border:1px solid #e96f26;" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRISuukVSb_iHDfPAaDKboFWXZVloJW9XXiwGYFab-QwlAYQ3zFsx4fToY9ijcVNU5ieKk&usqp=CAU"
                 width="36" class="rounded-circle me-2" alt="user-image">
            <div class="fw-semibold">Welcome! <?= ucfirst($_SESSION['user_name']); ?></div>
          </div>
          <div class="d-grid gap-2">
            <a href="index.php?file=password/update" class="btn btn-outline-secondary btn-sm">
              <i class="mdi mdi-account-circle me-1"></i> Change Password
            </a>
            <a href="logout.php" class="btn btn-outline-danger btn-sm">
              <i class="mdi mdi-logout me-1"></i> Logout
            </a>
          </div>
        </div>

      </div>
    </div>
  </nav>
</div>

<!-- ========== Topbar End ========== -->

<!-- ========== Topbar Start ========== --><!-- ========== Topbar Start ========== -->
<div class="navbar-custom d-none d-md-block">
  <div class="topbar container-fluid d-flex justify-content-between align-items-center position-relative p-0 ps-1">

    <!-- Brand Logo -->
    <a href="" class="logo logo-light d-flex align-items-center">
      <span class="logo-lg">
        <img src="assets/images/logo.png" alt="logo">
      </span>
      <span class="logo-sm">
        <img src="assets/images/logo.png" alt="small logo">
      </span>
    </a>

    <!-- ✅ CENTERED MENU START -->
    <ul class="topbar-menu d-flex align-items-center gap-1 p-0 mx-auto justify-content-center">
      <?php
      $current_file = isset($_GET['file']) ? $_GET['file'] : ''; // Get current file from URL
      $main_screens  = main_screen();
      foreach ($main_screens as $main_key => $main_value) {
          if (in_array($main_value['unique_id'], $_SESSION['main_screens'])) {
              $menu_main_name = disname($main_value["screen_main_name"]);
              $menu_main_icon = $main_value["icon_name"] ? '<i class="mdi ' . $main_value["icon_name"] . '"></i>' : '';
      ?>
      
      <?php
      $is_main_menu_active = false;
      $screen_sections = section_name('', $main_value['unique_id']);

      foreach ($screen_sections as $section_key => $section_value) {
          if (in_array($section_value['unique_id'], $_SESSION['sections'])) {
              $user_screens = user_screen('', $section_value['unique_id']);
              foreach ($user_screens as $screens_key => $screens_value) {
                  if (in_array($screens_value['unique_id'], $_SESSION['screens'])) {
                      $menu_screen_folder = $screens_value["folder_name"];
                      $menu_url = $menu_screen_folder . "/list";
                      if ($current_file === $menu_url) {
                          $is_main_menu_active = true;
                          break 2; // Exit both loops early
                      }
                  }
              }
          }
      }
      ?>

      <li class="dropdown dropdown-mega d-none d-xl-block">
        <a class="nav-link dropdown-toggle arrow-none waves-effect waves-light <?php echo $is_main_menu_active ? 'active mainmenuec fw-bold' : ''; ?>" 
           data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
          <?php echo $menu_main_name . ' ' . $menu_main_icon; ?>
        </a>

        <div class="dropdown-menu dropdown-megamenu">
          <div class="row">
            <div class="col-sm-12">
              <div class="row">
                <?php
                $screen_sections = section_name('', $main_value['unique_id']);
                foreach ($screen_sections as $section_key => $section_value) {
                    if (in_array($section_value['unique_id'], $_SESSION['sections'])) {
                        $menu_section_name = disname($section_value["section_name"]);
                        ?>
                        <div class="col">
                          <h5 class="text-primary mt-0"><?php echo $menu_section_name; ?></h5>
                          <ul class="list-unstyled megamenu-list">
                            <?php
                            $user_screens = user_screen('', $section_value['unique_id']);
                            foreach ($user_screens as $screens_key => $screens_value) {
                                if (in_array($screens_value['unique_id'], $_SESSION['screens'])) {
                                    $menu_screen_name = disname($screens_value["screen_name"]);
                                    $menu_screen_folder = $screens_value["folder_name"];
                                    $menu_url = $menu_screen_folder . "/list";

                                    $is_active = ($current_file === $menu_url);
                            ?>
                              <li class="<?php echo $is_active ? 'active' : ''; ?>">
                                <a href="index.php?file=<?php echo $menu_url; ?>" 
                                   class="<?php echo $is_active ? 'text-warning fw-bold' : ''; ?>" 
                                   aria-current="<?php echo $is_active ? 'page' : ''; ?>">
                                  <?php echo $menu_screen_name; ?>
                                </a>
                              </li>
                            <?php
                                }
                            }
                            ?>
                          </ul>
                        </div>
                <?php
                    }
                }
                ?>
              </div>
            </div>
          </div>
        </div>
      </li>
      <?php
          }
      }
      ?>
    </ul>
    <!-- ✅ CENTERED MENU END -->

    <!-- Right Side: Profile Dropdown -->
    <ul class="topbar-menu d-flex align-items-center gap-1">
      <li class="dropdown">
        <a class="nav-link dropdown-toggle arrow-none nav-user waves-effect waves-light" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
          <img style="border: 1px solid #e96f26;" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRISuukVSb_iHDfPAaDKboFWXZVloJW9XXiwGYFab-QwlAYQ3zFsx4fToY9ijcVNU5ieKk&usqp=CAU" width="32" class="rounded-circle d-flex" alt="user-image">
          <span class="d-lg-flex flex-column gap-1 d-none">
            <!--<span class="my-0"><?=ucfirst($_SESSION['user_name']);?> </span>--> <span class="mdi mdi-chevron-down"></span>
          </span>
        </a>
        <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated profile-dropdown">
          <div class="dropdown-header noti-title">
            <h6 class="text-overflow m-0">Welcome! <?=ucfirst($_SESSION['user_name']);?></h6>
          </div>
          <a href="index.php?file=password/update" class="dropdown-item"><i class="mdi mdi-account-circle me-1"></i><span>Change Password</span></a>
          <a href="logout.php" class="dropdown-item"><i class="mdi mdi-logout me-1"></i><span>Logout</span></a>
        </div>
      </li>
    </ul>

  </div>
</div>
<!-- ========== Topbar End ========== -->

<!-- Optional: Bootstrap JS bundle (needed for dropdowns) -->
<!--<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>-->



 
<!-- ✅ Add this CSS to your stylesheet -->
<style>
/* Show dropdown on hover 
.navbar-custom .dropdown-mega:hover .dropdown-menu {
    display: block;
    visibility: visible;
    opacity: 1;
    transform: translateY(0);
}*/

/* Optional: Smooth dropdown appearance 
.dropdown-megamenu {
    transition: opacity 0.2s ease, transform 0.2s ease;
    opacity: 0;
    transform: translateY(10px);
    visibility: hidden;
    display: block !important; /* Required for transition */
    pointer-events: auto;
    position: absolute;
    top: 100%;
    left: 0;
    z-index: 1000;
}
*/
    .topbar-menu li.active > a {
        color: #0d6efd; /* Bootstrap Primary */
        font-weight: bold;
    }

   /* .topbar-menu li.active {
        border-bottom: 2px solid #0d6efd;
    }*/
</style>
