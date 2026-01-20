<?php require __DIR__ . '/../layouts/header.php'; ?>

<div class="dashboard-header" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: white; padding: 30px; border-radius: 12px; margin-bottom: 30px;">
    <h1>👨‍🏫 Gestão de Professores</h1>
    <p>Visualize e gerencie os professores da rede.</p>
</div>

<div class="list-section">
    <h3 style="margin-bottom: 20px; color: #1e293b;">Professores Cadastrados</h3>
    
    <div style="margin-bottom: 20px;">
        <input type="text" id="searchProf" onkeyup="filterTable()" placeholder="Buscar por nome..." class="form-control" style="max-width: 300px; padding: 10px; border-radius: 20px; border: 1px solid #ccc;">
    </div>

    <table class="data-table" id="profTable">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Escola (ID/Nome)</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
             <?php if(empty($professors)): ?>
                <tr><td colspan="3">Nenhum professor encontrado.</td></tr>
            <?php else: ?>
                <?php foreach($professors as $p): ?>
                    <tr>
                        <td>
                            <?= htmlspecialchars($p['name']) ?>
                            <?php if (!empty($p['is_monitor'])): ?>
                                <span style="background-color: #17a2b8; color: #fff; padding: 2px 5px; border-radius: 4px; font-size: 11px; margin-left: 5px;">M.A.E</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($p['school_name'] ?? $p['school_id']) ?></td>
                        <td>
                            <a href="<?= url('admin/user/reset-password?id='.$p['id']) ?>" class="btn-icon" title="Resetar Senha para '123456'" onclick="return confirm('Tem certeza que deseja resetar a senha deste usuário para 123456?')"><i class="fas fa-key"></i></a>
                            <a href="<?= url('admin/user/delete?id='.$p['id']) ?>" class="btn-icon" style="color: red;" title="Excluir Usuário" onclick="return confirm('Tem certeza que deseja excluir este usuário permanentemente?')"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
function filterTable() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("searchProf");
  filter = input.value.toUpperCase();
  table = document.getElementById("profTable");
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[0];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }       
  }
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
