create view view_staff_permission as
select staff_id,entry_date,unique_id, 'daily_attendance' as table_type from daily_attendance where attendance_type = 1 and day_status = 3 and is_delete = 0 union select staff_id, from_date,unique_id, 'leave_details' as table_type from leave_details where day_type = 6 and is_approved = 1 and is_delete = 0

create view view_staff_current_date_status as 
	select staff_id,entry_date from daily_attendance where attendance_type = 1 and is_delete = 0 union select staff_id, from_date from leave_details_sub where hr_approved = 1 and is_delete = 0 and day_type != 3 union select staff_id, from_date from view_work_from_home 

-- create view view_absent_staff as 
-- 	select staff.unique_id from staff join view_staff_current_date_status where unique_id not in (select staff_id from view_staff_current_date_status) and staff.is_active = 1 and staff.is_delete = 0 and view_staff_current_date_status.entry_date = '2021-08-13' GROUP BY staff.unique_id

create view view_full_day_leave as  
	select entry_date,from_date,staff_id,day_type,unique_id from leave_details_sub where is_delete = 0 and day_type = 1 AND cancel_status = 0 AND hr_approved = 1 union select entry_date,entry_date as from_date,staff_id,day_status as day_type,unique_id from daily_attendance where attendance_type = 1 and day_status = 5

create view view_work_from_home as
	select a.entry_date,a.from_date,a.staff_id,a.day_type,a.unique_id from leave_details_sub as a join daily_attendance as b on b.entry_date = a.from_date and a.staff_id=b.staff_id where b.attendance_type = 1 and a.day_type = 3 and a.hr_approved = 1 and a.is_delete = 0 