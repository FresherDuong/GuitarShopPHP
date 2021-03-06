<div class="row">
    <div class="col-md-12">
        <!-- Begin: life time stats -->
        <div class="portlet">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-shopping-cart"></i><?= $data['Title'] ?>
                </div>
            </div>
            <div class="portlet-body">
                <div class="table-container">
                    <table class="table table-bordered table-hover table-striped" style="border: 2px solid #121427">
                        <thead style="background-image: linear-gradient(to right, #121427, #4a144f) ; color: white">
                        <tr style="text-align: center; vertical-align: middle">
                            <td style="width: 3%">#</td>
                            <td style="width: 10%">Ngày đặt</td>
                            <td style="width: 7%">Ghi chú</td>
                            <td style="width: 20%">Tình trạng</td>
                            <td style="width: 10%">Tổng tiền</td>
                            <td style="width: 15%">Action</td>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($data['data'] as $value => $item) : ?>
                            <tr style="text-align: center; <?php if($item->tinhtrang === "Đã gửi") echo "background-color : #85ec85"; elseif($item->tinhtrang === "Đã hủy") echo "background-color : #ef5a5a"; else echo "background-color : #d2f73d";?>">
                                <td style="vertical-align: middle">#</td>
                                <td style="vertical-align: middle"><?= $item->ngay ?></td>
                                <td style="vertical-align: middle"><?= $item->ghichu ?></td>
                                <td style="vertical-align: middle"><?= $item->tinhtrang ?></td>
                                <td style="vertical-align: middle"><?= func::formatMoney($item->tongtien) . ' vnd' ?></td>
                                <td style="vertical-align: middle">
                                    <a href="Xulydonhang/viewInfor/<?= $item->id ?>" target="_blank">
                                        <button class="btn btn-default btn-xs purple nhaphang <?php if($item->tinhtrang !== "Chưa xét duyệt") echo "hidden";?>" style="color: white!important;"
                                                value="<?= $item->id ?>" ><i class="fa fa-download"> Xử lý đơn hàng</i></button>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- End: life time stats -->
    </div>
</div>
<style>
    th {
        text-align: center;
    }
</style>
<script src="/backend/App/Views/User/js/sweetalert2.all.min.js"></script>
<script>
    $('#donhang-li').addClass("active");
    $('#donhang-span').addClass("selected");

    $(document).ready(function () {
    })
</script>
<script src="/backend/Public/js/jquery.form.min.js" type="text/javascript"></script>