<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Covid19</title>

        <!-- Fonts -->
        
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="{{ asset('css/style.css') }}">
		
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>

        <!-- Styles -->
        <style>
           
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">          
			<div class="heading_main"><h2 class="text-center">Covid 19 Stats (India)</h2></div>			    
            <div class="container">
				<div class="row">					
					<div class="col-md-8">					
						<h4 class="text-center">Age Stats</h4>
						<table class="table table-bordered">
							<thead class="age_cat_thd">
								<tr>
									<th>Category</th>
									<th>Below 20</th>
									<th>21 - 30</th>
									<th>31 - 40</th>
									<th>41 - 50</th>
									<th>51 - 60</th>
									<th>Above 60</th>
									<th>Undefined</th>
								</tr>
							</thead>
							<tbody>
								@foreach($age_stats as $catg => $age_dels)
								    <tr>
										<td class="age_cat"><b>{{$catg}}</b></td>
										<td>{{$age_dels['below_20']}}</td>
										<td>{{$age_dels['21_30']}}</td>
										<td>{{$age_dels['31_40']}}</td>
										<td>{{$age_dels['41_50']}}</td>
										<td>{{$age_dels['51_60']}}</td>
										<td>{{$age_dels['above_60']}}</td>
										<td>{{$age_dels['Undefined']}}</td>
									</tr>
								@endforeach
							</tbody>
						</table>	
					</div>
				</div>
                <h4 class="text-center">State Wise Stats</h4>
				<table class="table table-striped table_wrp">
					<thead class="main_table">
						<tr>
							<th>State</th>
							<th>Confirmed</th>
							<th>Active</th>
							<th>Recovered</th>
							<th>Death</th>
							<th>District wise</th>
							<th>Last updated</th>
						</tr>
					</thead>
					<tbody>
						<?php
							foreach($state_wise_details as $state => $state_details){ 
								if($state == 'Total'){
						?>
									<tr class="total_confirm">
										<td><b><?php echo $state;?></b></td>
										<td><?php echo $state_details['confirmed'];?></td>
										<td><?php echo $state_details['active'];?></td>
										<td><?php echo $state_details['recovered'];?></td>
										<td><?php echo $state_details['death'];?></td>
										<td></td>
										<td><?php echo $state_details['last_updated'];?></td>
									</tr>
						<?php
								
								}else{				
						?>
									<tr>
										<td><b><?php echo $state;?></b></td>
										<td><?php echo $state_details['confirmed'];?></td>
										<td><?php echo $state_details['active'];?></td>
										<td><?php echo $state_details['recovered'];?></td>
										<td><?php echo $state_details['death'];?></td>
										<td>
											<button class="btn_show" id="btn{{str_replace(' ', '', $state)}}" onclick="showDiscTable('{{str_replace(' ', '', $state)}}')">Show</button>
											<table class="table table-bordered" onclick="showDiscTable('{{str_replace(' ', '', $state)}}')" style="display:none" id="table_d_wrap_{{str_replace(' ', '', $state)}}">
												<tr class="table_sub">
													<th>District</th>
													<th>Confirmed</th>
													<th>Active</th>
													<th>Recoverd</th>
													<th>Deceased</th>
												</tr>
												<?php 
													if(sizeof($state_details['dist_details'])> 0){
														foreach($state_details['dist_details'] as $disct => $confirm_case){
												?>
															<tr>
																<td><b><?php echo $disct;?></b></td>
																<td><?php echo $confirm_case['confirmed'];?></td>
																<td><?php echo $confirm_case['active'];?></td>
																<td><?php echo $confirm_case['recovered'];?></td>
																<td><?php echo $confirm_case['deceased'];?></td>
															</tr>
												<?php
														}						
													}
												?>
											</table>
										</td>
										<td><?php echo $state_details['last_updated'];?></td>				
									</tr>	
						<?php
								}
							}
						?>
					</tbody>
				</table>
            </div>
        </div>
		<script>
			function showDiscTable(state){
				console.log('22');
				$('#btn'+state).toggle();
				$('#table_d_wrap_'+state).toggle();
			}
		</script>
    </body>
</html>
