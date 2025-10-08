create view view_cl_leave_type_two as  
select date_format(from_date,'%Y-%m') as date,staff_id,leave_type as cl_leave_type,COUNT(leave_type) as leave_type from leave_details_sub where hr_approved = 1 and is_delete = 0 and leave_type = 2 GROUP by staff_id,date_format(from_date,'%Y-%m')

create view view_cl_leave_type_eight as  
select date_format(from_date,'%Y-%m') as date,staff_id,leave_type as cl_leave_type,COUNT(leave_type) as leave_type from leave_details_sub where hr_approved = 1 and is_delete = 0 and leave_type = 8 GROUP by staff_id,date_format(from_date,'%Y-%m')

create view view_cl_leave_type_half_eight as  
select date_format(from_date,'%Y-%m') as date,staff_id,leave_type as cl_leave_type,COUNT(half_leave_type) as leave_type from leave_details_sub where hr_approved = 1 and is_delete = 0 and half_leave_type = 8 GROUP by staff_id,date_format(from_date,'%Y-%m')

create view view_cl_half_count as
	select date,staff_id,cl_leave_type,leave_type,"leave_type" as type_cl from view_cl_leave_type_eight union
	select date,staff_id,cl_leave_type,leave_type,"half_leave_type" as type_cl from view_cl_leave_type_half_eight union select date,staff_id,cl_leave_type,leave_type,"leave_type_two" as type_cl from view_cl_leave_type_two 

create view view_comp_off_leave_type_full as  
select date_format(from_date,'%Y-%m') as date,staff_id,leave_type as comp_off_leave_type,COUNT(leave_type) as leave_type from leave_details_sub where hr_approved = 1 and is_delete = 0 and leave_type = 4 GROUP by staff_id,date_format(from_date,'%Y-%m')

create view view_comp_off_leave_type_half as  
select date_format(from_date,'%Y-%m') as date,staff_id,leave_type as comp_off_leave_type,COUNT(leave_type) as leave_type from leave_details_sub where hr_approved = 1 and is_delete = 0 and leave_type = 10 GROUP by staff_id,date_format(from_date,'%Y-%m')

create view view_comp_off_leave_type_half_leave as  
select date_format(from_date,'%Y-%m') as date,staff_id,leave_type as comp_off_leave_type,COUNT(half_leave_type) as leave_type from leave_details_sub where hr_approved = 1 and is_delete = 0 and half_leave_type = 10 GROUP by staff_id,date_format(from_date,'%Y-%m')

create view view_comp_off_half_count as
	select date,staff_id,comp_off_leave_type,leave_type,"full_leave_type" as type_comp_off from view_comp_off_leave_type_half union
	select date,staff_id,comp_off_leave_type,leave_type,"half_leave_type" as type_comp_off from view_comp_off_leave_type_half_leave union select date,staff_id,comp_off_leave_type,leave_type,"split_leave_type" as type_comp_off from view_comp_off_leave_type_full 

create view view_leave_full as 
	select date_format(from_date,'%Y-%m') as date,staff_id,day_type,leave_type as full_day_leave,COUNT(leave_type) as leave_days from leave_details_sub where hr_approved = 1 and is_delete = 0 GROUP by staff_id,date_format(from_date,'%Y-%m'), leave_type,day_type

create view view_leave_half as 
	select date_format(from_date,'%Y-%m') as date,staff_id,day_type,half_leave_type as half_day_leave,COUNT(leave_type) as leave_days from leave_details_sub where hr_approved = 1 and half_leave_type != '' and is_delete = 0 GROUP by staff_id,date_format(from_date,'%Y-%m'), half_leave_type,day_type

create view view_leave as 
	select date,staff_id,day_type,full_day_leave as leave_type,leave_days from view_leave_full union
	select date,staff_id,day_type,half_day_leave as leave_type,leave_days from view_leave_half 