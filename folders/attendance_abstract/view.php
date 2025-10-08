//present
SELECT entry_date,staff_id,day_status as present from daily_attendance where  attendance_type = 1 and is_delete = 0



SELECT entry_date,staff_id,
(CASE WHEN day_status = 1 THEN day_status ELSE 'null' END) as present,
(CASE WHEN day_status = 2 THEN day_status ELSE 'null' END) as late,
(CASE WHEN day_status = 3 THEN day_status ELSE 'null' END) as permission,
(CASE WHEN day_status = 4 THEN day_status ELSE 'null' END) as half_day
from daily_attendance where  is_delete = 0 and attendance_type = 1  group by staff_id,entry_date

SELECT from_date as entry_date,staff_id,
(CASE WHEN leave_type = 1 THEN leave_type ELSE 'null' END) as cl,
(CASE WHEN leave_type = 2 THEN leave_type ELSE 'null' END) as comp_off,
(CASE WHEN leave_type = 3 THEN leave_type ELSE 'null' END) as lop
from leave_details_sub where  is_delete = 0 and hr_approved = 1  group by staff_id,from_date

select a.entry_date,a.staff_id,a.present,a.late,a.permission,a.half_day,b.cl,b.comp_off,b.lop from view_day_history as a left join view_leave_history as b on a.entry_date = b.entry_date and a.staff_id = b.staff_id union select a.entry_date,a.staff_id,b.present,b.late,b.permission,b.half_day,a.cl,a.comp_off,a.lop from view_leave_history as a left join view_day_history as b on a.entry_date = b.entry_date and a.staff_id = b.staff_id ORDER BY `comp_off` DESC