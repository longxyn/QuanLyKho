<?php
include_once("models/loc_tk_sp.php");
$p = new tmdt();
// Tính tổng số trang
// Ẩn tất cả các thông báo lỗi
error_reporting(0);

// Hoặc ẩn thông báo lỗi chỉ định
// error_reporting(E_ERROR | E_WARNING | E_PARSE);

// Code của bạn tiếp theo ở đây
// ...

// Ví dụ:
// Lệnh gán giá trị mặc định cho biến nếu không được định nghĩa
$records_per_page = isset($records_per_page) ? $records_per_page : 10;

// Chia mảng (đảm bảo biến $donban là mảng trước khi sử dụng array_slice())
$donban = isset($donban) && is_array($donban) ? array_slice($donban, 0, $records_per_page) : array();
$total_pages = ceil(count($kho_sp) / $records_per_page);

// Lấy trang hiện tại từ tham số truyền vào URL, mặc định là trang 1
$current_page = isset($_GET['page']) ? $_GET['page'] : 1;

// Xác định vị trí bắt đầu của mỗi trang trong mảng đơn hàng
$start_index = ($current_page - 1) * $records_per_page;

// Lấy danh sách đơn hàng cho trang hiện tại
$donban_page = array_slice($donban, $start_index, $records_per_page);

if (isset($_POST['filterStatus'])) {
    $filterStatus = $_POST['filterStatus'];
    if ($filterStatus !== '') {
        $donban_page = kho_sp::filterByStatus($filterStatus, $start_index, $records_per_page);
    }
}
?>
<style>
  .chart{
    height: 10px;
  }

</style>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<h1 class="h3 mb-2 text-center text-gray-800 ">Kho Sản Phẩm</h1>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Danh sách kho sản phẩm</h6>
    </div>

    <div class="card-body">
    <canvas id="productChart" width="400" height="200"></canvas>


        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                <?php 
				    $p->loadMenuKho('select * from kho_sp order by ten_kho_sp asc');
			    ?>
                </thead>
                <tfoot>
                <tr>
                <?php
				$id_kho_sp=$_REQUEST['id_kho_sp'];
				if($id_kho_sp>=0)
				{
					$p->xuatsanpham("select * from sanpham where id_kho_sp='$id_kho_sp' order by id asc");
				}
				else
				{
					$p->xuatsanpham("select * from sanpham order by Id asc");
				}
			?>
                </tr>
                </tfoot>
                <tbody>
        
        </div>
      <canvas id="productChart" width="200px" height="100px">

        <?php
              // Kết nối đến cơ sở dữ liệu của bạn
              $servername = "localhost";
              $username = "root";
              $password = "";
              $dbname = "db_quanlykho";

              $conn = new mysqli($servername, $username, $password, $dbname);

              // Kiểm tra kết nối
              if ($conn->connect_error) {
                  die("Kết nối đến cơ sở dữ liệu thất bại: " . $conn->connect_error);
              }

              // Truy vấn để lấy dữ liệu từ bảng sản phẩm với điều kiện id_kho_sp
              $sql = "SELECT  TenSP, SoLuong FROM SanPham WHERE id_kho_sp=?";
              $stmt = $conn->prepare($sql);

              // Kiểm tra lỗi prepare
              if ($stmt === false) {
                  die('Lỗi truy vấn: ' . $conn->error);
              }

              // Bind giá trị vào biến
              $id_kho_sp = $_GET['id_kho_sp']; // Thay bằng giá trị thực tế hoặc lấy từ nguồn dữ liệu
              $stmt->bind_param('i', $id_kho_sp);

              // Thực hiện truy vấn
              $stmt->execute();

              // Lấy kết quả
              $result = $stmt->get_result();

              // Mảng để lưu trữ dữ liệu cho biểu đồ
              $labels = [];
              $data = [];

              // Lặp qua các dòng dữ liệu từ bảng sản phẩm
              while ($row = $result->fetch_assoc()) {
                  $labels[] = $row['TenSP'];
                  $data[] = $row['SoLuong']; // Bạn cần thay đổi 'so_luong_san_pham' thành tên cột chứa số liệu thực tế của bạn
              }

              // Đóng kết nối đến cơ sở dữ liệu
              $stmt->close();
              $conn->close();
              ?>

              <script>
                  function loadChart(id_kho_sp) {
                    
                      var xhttp = new XMLHttpRequest();
                      xhttp.onreadystatechange = function() {
                          if (this.readyState == 4 && this.status == 200) {
                              var responseData = JSON.parse(this.responseText);

                              // Cập nhật dữ liệu biểu đồ
                              myChart.data.labels = responseData.labels;
                              myChart.data.datasets[0].data = responseData.data;
                              myChart.update();
                          }
                      };

                      xhttp.open("GET", "getChartData.php?id_kho_sp=" + id_kho_sp, true);
                      xhttp.send();
                  }
                  

                  var data = {
                      labels: <?php echo json_encode($labels); ?>,
                      datasets: [{
                          label: 'Số lượng sản phẩm',
                          data: <?php echo json_encode($data); ?>,
                          backgroundColor: [
                              'rgba(255, 99, 132, 0.2)',
                              'rgba(54, 162, 235, 0.2)',
                              'rgba(255, 206, 86, 0.2)',
                          ],
                          borderColor: [
                              'rgba(255, 99, 132, 1)',
                              'rgba(54, 162, 235, 1)',
                              'rgba(255, 206, 86, 1)',
                          ],
                          borderWidth: 1,
                      }],
                  };

                  var options = {
                      scales: {
                          y: {
                              beginAtZero: true,
                          },
                      },
                  };

                  var ctx = document.getElementById('productChart').getContext('2d');
                  var myChart = new Chart(ctx, {
                      type: 'bar',
                      data: data,
                      options: options,
                  });

                  // Mặc định, tải dữ liệu cho kho đầu tiên khi trang được nạp
                  loadChart(<?php echo $id_kho_sp; ?>);
              </script>
      </canvas>
    </div>
    <div id="footer"></div>
</div>
</body>
</html>