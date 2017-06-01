  <div class="row">
    <div class="col-md-6">
      <div class="page-header">
        <h2>Active hours</h2>
      </div>

      <div id="c">
      </div>

      <script>

      var chart = c3.generate({
          bindto: '#c',
          data: {
            json: <?php echo json_encode($hours); ?>,
            keys: {
              value: ['hour', 'connections'],
            },
            x: 'hour',
            y: 'connections',
            type: 'bar',
          },
          axis: {
            x: {
              type: 'category',
              tick: {
                culling: false,
                rotate: 90
              }
            }
          }
      });

      </script>
    </div>
    <div class="col-md-6">
      <div class="page-header">
        <h2>Active roles
          <small><i class="fa fa-flask"></i> Experimental</small> 
        </h2>
      </div>
      <div id="d">
      </div>
    </div>
    <script>
    var chart = c3.generate({
        bindto: '#d',
        data: {
          json: <?php echo json_encode($roles); ?>,
          keys: {
            value: ['job', 'minutes'],
          },
          x: 'job',
          y: 'minutes',
          type: 'bar',
        },
        axis: {
          x: {
            type: 'category',
            tick: {
              culling: false,
              rotate: 90
            }
          }
        }
    });
    </script>
  </div>