create view view_staff_check_in as 
select staff_id,entry_date,entry_time,latitude,longitude,attendance_type as check_in,day_status from daily_attendance where is_delete = 0 and attendance_type = 1

create view view_staff_check_out as 
select staff_id,entry_date,entry_time,latitude,longitude,attendance_type as check_out from daily_attendance where is_delete = 0 and attendance_type = 2

create view view_staff_break_in as 
select staff_id,entry_date,entry_time,latitude,longitude,attendance_type as break_in from daily_attendance where is_delete = 0 and attendance_type = 3

create view view_staff_break_out as 
select staff_id,entry_date,entry_time,latitude,longitude,attendance_type as break_out from daily_attendance where is_delete = 0 and attendance_type = 4

create view view_staff_attendance_report as
select a.entry_date,a.staff_id,a.latitude,a.longitude,a.entry_time as check_in_time,b.entry_time as check_out_time,c.entry_time as break_in_time,d.entry_time as break_out_time,a.day_status from view_staff_check_in as a join view_staff_check_out as b on a.staff_id = b.staff_id and a.entry_date = b.entry_date join view_staff_break_in as c on a.staff_id = c.staff_id and a.entry_date = c.entry_date join view_staff_break_out as d on a.staff_id = d.staff_id and a.entry_date = d.entry_date