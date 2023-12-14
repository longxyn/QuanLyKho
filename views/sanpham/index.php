
<?php
require_once ('models/sanpham.php');

if (isset($_POST['filterByDateRange'])) {
    $startDate = isset($_POST['startDate']) ? date('Y-m-d', strtotime($_POST['startDate'])) : null;
    $endDate = isset($_POST['endDate']) ? date('Y-m-d', strtotime($_POST['endDate'])) : null;

    // Validate and sanitize the dates if needed
    // Implement your validation logic here

    $filteredProducts = SanPham::all($startDate, $endDate);
} else {
    // If the form is not submitted, retrieve all products
    $filteredProducts = SanPham::all();
}
?>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Lấy ra ô checkbox "Chọn tất cả"
        var selectAllCheckbox = document.getElementById("selectAll");

        // Lấy ra tất cả các ô checkbox trong tbody
        var checkboxes = document.querySelectorAll("tbody input[type='checkbox']");

        // Thêm sự kiện click cho ô checkbox "Chọn tất cả"
        selectAllCheckbox.addEventListener("click", function () {
            // Duyệt qua tất cả các ô checkbox trong tbody và đặt trạng thái checked giống với ô "Chọn tất cả"
            checkboxes.forEach(function (checkbox) {
                checkbox.checked = selectAllCheckbox.checked;
            });
        });

        // Thêm sự kiện click cho mỗi ô checkbox trong tbody
        checkboxes.forEach(function (checkbox) {
            checkbox.addEventListener("click", function () {
                // Nếu có một ô checkbox không được chọn, bỏ chọn ô "Chọn tất cả"
                if (!checkbox.checked) {
                    selectAllCheckbox.checked = false;
                }
            });
        });
    });
</script>
<h1 class="h3 mb-2 text-center text-gray-800 ">Sản Phẩm</h1>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Danh sách Sản Phẩm</h6>
    </div>
    <form method="post" action="index.php">
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label for="startDate">Từ ngày:</label>
                <input type="date" id="startDate" name="startDate" class="form-control">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="endDate">Đến ngày:</label>
                <input type="date" id="endDate" name="endDate" class="form-control">
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="form-group">
                <button type="submit" name="filterByDateRange" class="btn btn-info" style="margin-top: 32px;">Lọc</button>
            </div>
        </div>
    </div>
</form>


<div class="card-body">
    <a href="index.php?controller=sanpham&action=insert" class="btn btn-primary mb-3">Thêm</a>
    <div class="table-responsive">
    <?php
    // Đảm bảo biến $selectedIds được khai báo và có giá trị
    $selectedIds = isset($selectedIds) ? $selectedIds : array();
    ?>
    <?php
            // Kiểm tra nếu form được submit
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                // Kiểm tra nếu có danh sách sản phẩm được chọn
                if (isset($_POST["selectedItems"]) && is_array($_POST["selectedItems"])) {
                    // Lấy danh sách các ID sản phẩm đã được chọn
                    $selectedIds = $_POST["selectedItems"];

                
                    header("Location: index.php?controller=sanpham&action=dplist");
                } else {
                    echo "<script>
                        alert('Yêu cầu chọn checkbox nếu bạn muốn điều phối nhiều sản phẩm');
                    </script>";
                }
            }

            $arrayOfIds = $selectedIds;

            // Chuyển đổi mảng thành chuỗi dạng (id1, id2, ...)
            $stringOfIds = '(' . implode(',', $arrayOfIds) . ')';

            // In kết quả
            echo $stringOfIds;
            // Trong file index.php
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            
            $_SESSION['stringOfIds'] = '(' . implode(',', $selectedIds) . ')';

            ?>
        <form method="post" action="">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="selectAll">  Chọn tất cả</th>
                        <th>ID</th>
                        <th>Tên sản phẩm</th>
                        <th>Đơn vị</th>
                        <th>Nhà cung cấp</th>
                        <th>Ngày Nhập</th>
                        <th>Số lượng</th>
                        <th>Trạng Thái</th>
                        <th>Kho sản phẩm</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>Click</th>
                        <th>ID</th>
                        <th>Tên sản phẩm</th>
                        <th>Đơn vị</th>
                        <th>Nhà cung cấp</th>
                        <th>Ngày Nhập</th>
                        <th>Số lượng</th>
                        <th>Trạng Thái</th>
                        <th>Kho sản phẩm</th>
                        <th>Action</th>
                    </tr>
                </tfoot>
                <tbody>
                    <?php foreach ($filteredProducts as $item) { ?>
                        <tr>
                            <td><input type="checkbox" name="selectedItems[]" value="<?= $item->Id ?>"></td>
                            <td><?= $item->Id    ?></td>
                            <td><?= $item->TenSP ?></td>
                            <td><?= $item->IdDVT ?></td>
                            <td><?= $item->IdNCC ?></td>
                            <td><?= $item->NgayNhap ?></td>
                            <td><?= $item->SoLuong ?></td>
                            <td>
                                <?php echo ($item->TrangThai == "1") ? "Đã Duyệt" : "Chưa Duyệt"; ?>
                            </td>
                            

                            <td><?= $item->id_kho_sp ?></td>
                            <td>
                                <a href="index.php?controller=sanpham&action=edit&id=<?= $item->Id ?>" class='btn btn-primary mr-3'>Sửa</a>
                                <button type="submit" name="dele" value="<?= $item->Id ?>" class='btn btn-danger'>Xóa</button>
                                <a href="index.php?controller=sanpham&action=dpsp&id=<?= $item->Id ?>" class='btn btn-success mr-3'>Điều phối</a>

                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
                        <button  type="submit" class="btn btn-success" name="submit">Xác nhận</button>
            <!-- Add other actions or buttons here -->
        </form>
    </div>
</div>




<?php
if(isset($_POST['dele'])){
    $id =$_POST['dele'];
    SanPham::delete($id);
}

?>


