<h4 class='my-5'>Display Time now and standard past to present times</h4>
<h4 class='my-5'>March 1 at 00:00 : <?php echo strtotime("2022-3-1") ?></h4>
<h4 class='my-5'>March 1 at 00:00 : <?php echo date('Y/m/d H:i', strtotime("2022-3-1")) ?></h4>

<table class="table">
  <thead>
    <tr class="table-active">
      <th scope="col">Jump</th>
      <th scope="col">Day of Week</th>
      <th scope="col">Readable Date</th>
      <th scope="col">Timestamp</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($timing as $key => $time) { ?>
    <tr class='<?php echo ($key == "now")? "bg-success text-white" : "" ?>'>
      <th><?php echo $key ?></th>
      <td><?php echo $time->dow ?></td>
      <td><?php echo $time->readable ?></td>
      <td><?php echo $time->timestamp ?></td>
    </tr>
    <?php } ?>
  </tbody>
</table>

<div class="">
  <div class="section my-3">
    <h3 class="mb-0">Use a custom time as reference</h3>
    <span class="small font-italic">Will change "now" to your selected date and time</span>
  </div>

  <form action="POST" class="jump">
    <div class="form-group row">
      <div class="col">
        <label for="date">Date</label>
        <input class="form-control" type="date" name="date" />
      </div>
      <div class="col">
        <label for="time">Time</label>
        <input class="form-control" type="time" name="time" />
      </div>
    </div>
    <div class="form-group">
      <button type="submit" class="btn btn-primary">Jump</button>
      <button type="submit" class="btn btn-danger">Reset</button>
    </div>
  </form>
</div>

