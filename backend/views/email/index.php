<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = $title;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="email-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <div style="border: solid 1px; padding: 10px; margin-bottom: 20px">
      <div class="row" style="margin-left: 3px; margin-bottom: 20px;">
        <div class="col-md-2">
          <lable style="font-size: 20px; font-weight: 700">YAHOO</lable>
        </div>
        <div class="col-md-2">
          <lable style="font-size: 16px; font-weight: 600;">TOTAL: <?= $summary["yahoo"]["total"] ?></lable>
        </div>
        <div class="col-md-2">
          <div class="row">
            <lable style="font-size: 16px; font-weight: 600; color: green">SUCCESS: <?= $summary["yahoo"]["success"] ?></lable>
          </div>
          <div class="row">
            <lable style="font-size: 16px; font-weight: 600; color: green">SUCCESS-2M: <?= $summary["yahoo"]["success_2m"] ?></lable>
          </div>
        </div>
        <div class="col-md-2">
          <lable style="font-size: 16px; font-weight: 600; color: green">EXPORT: <?= $summary["yahoo"]["export"] ?></lable>
        </div>
        <div class="col-md-2">
          <lable style="font-size: 16px; font-weight: 600; color: red">FAILED: <?= $summary["yahoo"]["failed"] ?></lable>
        </div>
        <div class="col-md-2">
          <lable style="font-size: 16px; font-weight: 600; color: blue">REMAIN: <?= $summary["yahoo"]["remain"] ?></lable>
        </div>
      </div>
      <!-- <div class="row" style="margin-left: 3px; margin-bottom: 20px;">
        <div class="col-md-2">
          <lable style="font-size: 20px; font-weight: 700">OUTLOOK</lable>
        </div>
        <div class="col-md-2">
          <lable style="font-size: 16px; font-weight: 600;">TOTAL: <?= $summary["outlook"]["total"] ?></lable>
        </div>
        <div class="col-md-2">
          <lable style="font-size: 16px; font-weight: 600; color: green">SUCCESS: <?= $summary["outlook"]["success"] ?></lable>
        </div>
        <div class="col-md-2">
          <lable style="font-size: 16px; font-weight: 600; color: green">EXPORT: <?= $summary["outlook"]["export"] ?></lable>
        </div>
        <div class="col-md-2">
          <lable style="font-size: 16px; font-weight: 600; color: red">FAILED: <?= $summary["outlook"]["failed"] ?></lable>
        </div>
        <div class="col-md-2">
          <lable style="font-size: 16px; font-weight: 600; color: blue">REMAIN: <?= $summary["outlook"]["remain"] ?></lable>
        </div>
      </div>
      <div class="row" style="margin-left: 3px;">
        <div class="col-md-2">
          <lable style="font-size: 20px; font-weight: 700">GMAIL</lable>
        </div>
        <div class="col-md-2">
          <lable style="font-size: 16px; font-weight: 600;">TOTAL: <?= $summary["gmail"]["total"] ?></lable>
        </div>
        <div class="col-md-2">
          <lable style="font-size: 16px; font-weight: 600; color: green">SUCCESS: <?= $summary["gmail"]["success"] ?></lable>
        </div>
        <div class="col-md-2">
          <lable style="font-size: 16px; font-weight: 600; color: green">EXPORT: <?= $summary["gmail"]["export"] ?></lable>
        </div>
        <div class="col-md-2">
          <lable style="font-size: 16px; font-weight: 600; color: red">FAILED: <?= $summary["gmail"]["failed"] ?></lable>
        </div>
        <div class="col-md-2">
          <lable style="font-size: 16px; font-weight: 600; color: blue">REMAIN: <?= $summary["gmail"]["remain"] ?></lable>
        </div>
      </div> -->
    </div>
    <div style="margin-bottom: 10px">
        <div class="upload-btn-wrapper">
          <button class="btn">Upload file</button>
          <input type="file" id="upload-file-btn" name="files[]"/>
        </div>
        <div class="upload-btn-wrapper" style="margin-left: 15px;">
          <button class="btn" style="color: red; border-color: red" id="delete-all-btn">Xóa toàn bộ</button>
        </div>
        <div class="upload-btn-wrapper" style="margin-left: 15px;">
          <button class="btn" style="color: blue; border-color: blue" id="export-success-btn">Xuất file thành công</button>
        </div>
        <div class="upload-btn-wrapper" style="margin-left: 15px;">
          <button class="btn" style="color: red; border-color: red" id="resest-all-btn">Reset Mail</button>
        </div>
        <div class="upload-btn-wrapper" style="margin-left: 15px;">
          <button class="btn" style="color: red; border-color: red" id="delete-by-status">Xóa theo Status</button>
        </div>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            'email:email',
            'password',
            'type',
            [
              'attribute' => 'import',
              'label' => 'Thử',
              'value' => function($email) {
                return $email->import == 1 ? "Đã thử" : "Chưa thử";
              }
            ],
            [
              'attribute' => 'status',
              'label' => 'Trạng thái',
              'format' => 'raw',
              'value' => function($email) {
                switch($email->status) {
                  case 0: return "---";
                  case -1: return "<span style='color: green; font-weight: 700;'>SUCCESS - 2M</span>";
                  case -2: return "<span style='color: green; font-weight: 700;'>SUCCESS - USED</span>";
                  case 1: return "<span style='color: green; font-weight: 700;'>Thành Công</span>";
                  case 2: return "<span style='color: red; font-weight: 700;'>(2) Blocked</span>";
                  case 3: return "<span style='color: red; font-weight: 700;'>(3) No code</span>";
                  case 4: return "<span style='color: red; font-weight: 700;'>(4) Failed Many</span>";
                  case 5: return "<span style='color: red; font-weight: 700;'>(5) Used</span>";
                  case 11: return "<span style='color: red; font-weight: 700;'>(11) Captcha</span>";
                  case 12: return "<span style='color: red; font-weight: 700;'>(12) Request Verification</span>";
                  case 13: return "<span style='color: red; font-weight: 700;'>(13) Code Mail</span>";
                  case 14: return "<span style='color: red; font-weight: 700;'>(14) Verify Code</span>";
                  case 15: return "<span style='color: red; font-weight: 700;'>(15) Final Submit</span>";
                  case 16: return "<span style='color: red; font-weight: 700;'>(16) Used</span>";
                  case 99: return "<span style='color: red; font-weight: 700;'>(99) Error</span>";
                  default: "---";
                }
              }
            ],
            [
              'attribute' => 'created_at',
              'label' => 'Ngày cập nhật',
              'value' => function($email) {
                $date = new DateTime($email->created_at, new DateTimeZone('Asia/Ho_Chi_Minh'));
                return $date->format('d-m-Y H:i:s') ;
              }
            ],
            [
              'attribute' => 'export',
              'label' => 'Xuất File',
              'format' => 'raw',
              'value' => function($email) {
                switch($email->export) {
                  case 0: return "---";
                  case 1: return "<span style='color: green; font-weight: 700;'>Done</span>";
                  default: "---";
                }
              }
            ],
            [
              'attribute' => 'dob',
              'label' => 'Ngày sinh',
              'format' => 'raw',
              'value' => function($email) {
                if (empty($email) || empty($email->dob)) {
                  return "---";
                }
                return $email->dob;
              }
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
        'pager' => [
          'firstPageLabel' => 'First',
          'lastPageLabel' => 'Last',
        ],
    ]); ?>
</div>
<!-- Modal -->
<div id="export-file-dialog" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Xuất file THÀNH CÔNG!</h4>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label for="amout">Số lượng</label>
          <input id="amout" name="amout" type="text" class="form-control" aria-describedby="basic-addon1">
        </div>
        <div class="form-group">
          <label for="sel1">Chọn loại Mail:</label>
          <select class="form-control" id="select-email">
            <option>OUTLOOK</option>
            <option>YAHOO</option>
            <option>GMAIL</option>
            <option>ALL</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button id="export-btn" type="button" class="btn btn-default" data-dismiss="modal">Xuất</button>
      </div>
    </div>

  </div>
</div>

<div id="delete-by-status-modal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Xóa MAIL!</h4>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label for="sel1">Chọn loại Mail:</label>
          <select class="form-control" id="select-delete-email">
            <option>USED</option>
            <option>NO CODE</option>
            <option>FAILED MANY</option>
            <option>BLOCKED</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button id="delete-status-btn" type="button" class="btn btn-default" data-dismiss="modal">Xóa</button>
      </div>
    </div>

  </div>
</div>
<style>
.upload-btn-wrapper {
  position: relative;
  overflow: hidden;
  display: inline-block;
}

.btn {
  border: 2px solid gray;
  color: gray;
  background-color: white;
  border-radius: 8px;
  font-size: 20px;
  font-weight: bold;
}

.upload-btn-wrapper input[type=file] {
  font-size: 100px;
  position: absolute;
  left: 0;
  top: 0;
  opacity: 0;
}
</style>

<?php 
$script = <<< JS
$(function() {
  $('#upload-file-btn').change(handleUploadFile);
  $('#delete-all-btn').click(handleDeleteAll);
  $('#resest-all-btn').click(handleResetAll);
  $('#export-success-btn').click(handleExportSuccess);
  $('#export-btn').click(execExport);
  $('#delete-by-status').click(handleDeleteByStatus);
  $('#delete-status-btn').click(execDelete);
});

function handleUploadFile(event) {
  let files = event.currentTarget.files;
  var formData = new FormData();
  $.each(files, function(key, value){
      formData.append(key, value);
  });

  $.ajax({
    url: '/email/upload',
    type: 'POST',
    data: formData,
    success:function(data){
      // location.reload();
      window.location.href = "/email";
    },
    cache: false,
    contentType: false,
    processData: false
  });
}

function handleDeleteAll(event) {
  let result = confirm("Có chắc xóa toàn bộ EMAIL.");
  if (!result) return;
  $.ajax({
    url: '/email/delete-all',
    type: 'POST',
    success:function(data){
      location.reload();
    },
    cache: false,
    contentType: false,
    processData: false
  });
}

function handleResetAll(event) {
  let result = confirm("Có chắc RESET toàn bộ EMAIL không thành công");
  if (!result) return;
  $.ajax({
    url: '/email/force-reset',
    type: 'POST',
    success:function(data){
      location.reload();
    },
    cache: false,
    contentType: false,
    processData: false
  });
}

function handleExportSuccess(event) {
  $('#export-file-dialog').modal();
  
}

function handleDeleteByStatus(event) {
  $('#delete-by-status-modal').modal();
}

function execDelete(event) {
  let type = $('#select-delete-email').val();
  console.log('===============type', type);
  if (type == "USED") {
    type = 5;
  } else if (type == "NO CODE") {
    type = 3;
  } else if (type == "FAILED MANY") {
    type = 4;
  } else if (type == "BLOCKED") {
    type = 2;
  } else {
    return;
  }

  let url = '/email/delete-by-status?status=' + type;

  window.open(url, '_blank');
  location.reload();
}

function execExport(event) {
  let amount = $('#amout').val();
  let type = $('#select-email').val();
  let url = '/email/download-success-file';

  let params = [];
  if (amount) params.push("amount="+amount);
  if (type && type != "ALL") params.push("type="+type);

  if (params.length > 0) url += "?" + params.join("&");

  window.open(url, '_blank');
  location.reload();
}

JS;

$this->registerJs($script);
?>
