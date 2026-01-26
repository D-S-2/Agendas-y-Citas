<?php
// 1. Definimos el título y si necesitamos CSS extra (en este caso no, usamos dashboard.css por defecto)
$page_title = "Panel Principal";

// 2. Incluimos la estructura
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
?>

<main class="main-content">
    <h1>Bienvenido al Sistema</h1>
    <p>Resumen del día en la clínica</p>
    <hr> <br>

    <div class="dashboard-cards">
        <div class="card" style="border-left-color: #3498db;">
            <div>
                <h3>0</h3>
                <p>Citas para Hoy</p>
            </div>
            <i class="far fa-calendar-check"></i>
        </div>

        <div class="card" style="border-left-color: #2ecc71;">
            <div>
                <h3>0</h3>
                <p>Pacientes Totales</p>
            </div>
            <i class="fas fa-users"></i>
        </div>

        <div class="card" style="border-left-color: #e74c3c;">
            <div>
                <h3>0</h3>
                <p>Pagos Pendientes</p>
            </div>
            <i class="fas fa-exclamation-circle"></i>
        </div>
        
        <div class="card" style="border-left-color: #f1c40f;">
            <div>
                <h3>$ 0.00</h3>
                <p>Ingresos del Día</p>
            </div>
            <i class="fas fa-dollar-sign"></i>
        </div>
    </div>

    <div style="background: white; padding: 20px; border-radius: 8px;">
        <h3>Próximas Citas</h3>
        <p><i>Aquí cargaremos la tabla de citas del día próximamente...</i></p>
    </div>

</main>

<?php
// 4. Cerramos estructura
require_once '../../includes/footer.php';
?>