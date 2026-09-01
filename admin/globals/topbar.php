 <header class="topbar">


     <div class="d-flex align-items-center">


         <!-- MOBILE TOGGLE -->

         <button
             type="button"
             class="sidebar-toggle me-3"
             id="sidebarToggle"
             aria-label="Open sidebar">

             <i class="bi bi-list"></i>

         </button>


         <div class="topbar-title">

             Admin Dashboard

         </div>


     </div>


     <!-- USER -->

     <div class="topbar-user">


         <div class="text-end">

             <div class="topbar-name">

                 <?= htmlspecialchars($fullName) ?>

             </div>


             <div class="topbar-access">

                 Administrator

             </div>

         </div>


         <div class="topbar-avatar">

             <?= htmlspecialchars($initials) ?>

         </div>


     </div>


 </header>