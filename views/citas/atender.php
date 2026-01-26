<?php
$page_title = "Consulta Médica";
$page_css = "atencion.css";

require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
require_once '../../models/Servicio.php';
require_once '../../config/db.php'; // Necesitamos conexión directa para buscar datos de la cita rápido

// 1. Validar ID de Cita
if (!isset($_GET['id_cita'])) {
    echo "<script>window.location.href='calendario.php';</script>";
    exit;
}
$id_cita = $_GET['id_cita'];

// 2. Obtener datos de la Cita y Paciente (Query rápido manual)
$db = new Database();
$conn = $db->getConnection();
$sql = "SELECT c.*, p.nombres, p.apellido_paterno, p.ci, p.fecha_nacimiento 
        FROM citas c 
        INNER JOIN pacientes p ON c.id_paciente = p.id_paciente 
        WHERE c.id_cita = :id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(":id", $id_cita);
$stmt->execute();
$cita = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$cita) { die("Cita no encontrada"); }

// 3. Obtener Servicios Disponibles
$servicioModel = new Servicio();
$listaServicios = $servicioModel->listarActivos();
?>

<main class="main-content">
    <div class="page-header">
        <h1>Registrar Atención</h1>
        <a href="calendario.php" style="color: #7f8c8d;">&larr; Volver a Agenda</a>
    </div>

    <div class="atencion-container">
        <div class="paciente-info">
            <h3><i class="fas fa-user-injured"></i> Paciente</h3>
            <p><strong>Nombre:</strong> <?php echo $cita['nombres'] . " " . $cita['apellido_paterno']; ?></p>
            <p><strong>CI:</strong> <?php echo $cita['ci']; ?></p>
            <hr>
            <p><strong>Motivo Cita:</strong><br> <?php echo $cita['motivo']; ?></p>
            <p><strong>Hora:</strong> <?php echo $cita['fecha_hora_inicio']; ?></p>
        </div>

        <div class="consulta-form">
            <form action="../../controllers/atencionController.php" method="POST" id="formAtencion">
                <input type="hidden" name="id_cita" value="<?php echo $id_cita; ?>">

                <div class="form-group">
                    <label style="font-size: 1.1rem; font-weight:bold;">Diagnóstico / Notas Clínicas:</label>
                    <textarea name="diagnostico" rows="5" class="form-control" required placeholder="Describa el estado del paciente y procedimiento realizado..."></textarea>
                </div>

                <div class="form-group">
                    <label style="font-size: 1.1rem; font-weight:bold;">Tratamientos Realizados:</label>
                    <p style="font-size: 0.9rem; color: #666;">Seleccione los servicios para facturación:</p>
                    
                    <div class="servicios-grid">
                        <?php foreach($listaServicios as $s): ?>
                        <label class="servicio-item">
                            <input type="checkbox" 
                                   name="servicios[]" 
                                   value="<?php echo $s['id_servicio']; ?>" 
                                   data-precio="<?php echo $s['precio']; ?>"
                                   onclick="calcularTotal()">
                            
                            <input type="hidden" name="precio_<?php echo $s['id_servicio']; ?>" value="<?php echo $s['precio']; ?>">
                            
                            <div>
                                <strong><?php echo $s['nombre']; ?></strong><br>
                                <span style="color: #27ae60;">Bs <?php echo number_format($s['precio'], 2); ?></span>
                            </div>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="total-precio">
                    Total Estimado: Bs <span id="totalDisplay">0.00</span>
                </div>

                <div style="text-align: right; margin-top: 20px;">
                    <button type="submit" class="btn-primary" style="font-size: 1.2rem; padding: 15px 30px;">
                        <i class="fas fa-check-circle"></i> Finalizar Atención
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>

<script>
function calcularTotal() {
    let total = 0;
    const checkboxes = document.querySelectorAll('input[name="servicios[]"]:checked');
    
    checkboxes.forEach((cb) => {
        total += parseFloat(cb.getAttribute('data-precio'));
    });
    
    document.getElementById('totalDisplay').innerText = total.toFixed(2);
}
</script>

<?php require_once '../../includes/footer.php'; ?>