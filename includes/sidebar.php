<aside class="sidebar">
    <nav>
        <ul>
            <li><a href="../admin/dashboard.php"><i class="fas fa-home"></i> Inicio</a></li>
            
            <li class="menu-title">Gestión</li>
            <li><a href="../citas/calendario.php"><i class="far fa-calendar-alt"></i> Agendas y Citas </a></li>
            <li><a href="../pacientes/lista.php"><i class="fas fa-users"></i> Pacientes</a></li>
            
            <li class="menu-title">Clínica</li>
            
            <li><a href="../servicios/lista.php"><i class="fas fa-list-ul"></i> Servicios y Tarifas</a></li>
            
            <li><a href="../pacientes/historia.php"><i class="fas fa-file-medical"></i> Historia Clínica</a></li>
            <li><a href="../pagos/index.php"><i class="fas fa-money-bill-wave"></i> Caja y Recibos</a></li>
            <li><a href="../pagos/reportes.php"><i class="fas fa-chart-line"></i> Reportes Financieros</a></li>
            
            <?php if($_SESSION['rol'] == 'ADMIN'): ?>
            <li class="menu-title">Administración</li>
            <li><a href="../admin/usuarios.php"><i class="fas fa-user-shield"></i> Usuarios</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</aside>