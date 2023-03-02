fun is_older (x : int * int * int, y : int * int * int) =
    let
	val y1 = #1 x
	val y2 = #1 y
	val m1 = #2 x
	val m2 = #2 y
	val d1 = #3 x
	val d2 = #3 y
    in
	if y1 < y2
	then true
	else if y1 > y2
	then false
	else
 	    if m1 < m2
	    then true
	    else if m1 > m2
	    then false
	    else
		if d1 < d2
		then true
		else if d1 > d2
		then false
		else
		    false
    end

fun number_in_month (dates : (int * int * int) list, month : int) =
    if null dates
    then 0
    else if (#2 (hd dates)) = month
    then 1 + number_in_month(tl dates, month)
    else number_in_month(tl dates, month)

fun number_in_months (dates: (int*int*int) list, month : int list) =
    if null month
    then 0
    else number_in_month(dates, hd month) + number_in_months(dates, tl month)


fun dates_in_month (dates: (int*int*int) list, month : int) =
    if null dates
    then []
    else if (#2 (hd dates)) = month
    then (hd dates) :: dates_in_month(tl dates, month)
    else dates_in_month(tl dates, month)

fun dates_in_months (dates : (int*int*int) list, month : int list) =
    if null month
    then []
    else dates_in_month(dates, hd month) @ dates_in_months(dates, tl month)
							 
fun get_nth(s : string list, n : int) =
    if n = 1
    then hd s
    else get_nth(tl s, n-1)

fun date_to_string (d : int*int*int) =
    let
	val month_list = ["January","February","March","April","May","June","July","August","September","October","November","December"] 
    in
	get_nth(month_list ,#2 d)^ " " ^ Int.toString(#3 d) ^ ", " ^ Int.toString(#1 d)
    end

fun number_before_reaching_sum (sum : int , sq : int list) =
    if hd sq >= sum
    then 0
    else 1 + number_before_reaching_sum(sum - (hd sq), (tl sq))


fun what_month (day:int) =
    let
	val days = [31,28,31,30,31,30,31,31,30,31,30,31]
    in
	1 + number_before_reaching_sum(day, days)
    end

fun month_range (day1 : int, day2 : int) =
    if day1 > day2
    then []
    else what_month(day1) :: month_range(day1+1, day2)
	     
fun oldest (dates: (int*int*int) list) =
    if null dates
    then NONE
    else
	let
	    val first_d = hd dates
	    val oldest_d = oldest(tl dates)
	in
	    if not (isSome oldest_d) orelse is_older (first_d, valOf oldest_d)
	    then SOME (first_d)
	    else oldest_d
	end
