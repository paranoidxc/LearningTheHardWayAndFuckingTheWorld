(* Dan Grossman, Coursera PL, HW2 Provided Code *)

(* if you use this function to compare two strings (returns true if the same
   string), then you avoid several of the functions in problem 1 having
   polymorphic types that may be confusing *)
fun same_string(s1 : string, s2 : string) =
    s1 = s2

(* put your solutions for problem 1 here *)
fun all_except_option (s,l) =
    case l of
	[] => NONE
      | x::l' => case same_string(s, x) of
		     true => SOME l'
		   | false => case all_except_option(s, l') of
				  NONE => NONE
				| SOME y => SOME(x::y) 

fun get_substitutions1 (l, s) =
    case l of
	[] => []
      | cur_l::l' => case all_except_option(s, cur_l) of
			 NONE => get_substitutions1(l', s)
		       | SOME y => y @  get_substitutions1(l', s)

fun get_substitutions2 (l, s) =
    let
	fun sub2_tail(l, s, acc) =
	    case l of
		[] => acc
	      | cur_l::l'=> case all_except_option(s, cur_l) of
				NONE => sub2_tail(l', s, acc)
			      | SOME y => sub2_tail(l', s, acc @ y)
    in
	sub2_tail(l, s, [])
    end	

fun similar_names (l, full_name:{first:string, middle:string, last:string}) =
    let fun ret(l) =
	    case l of
		[] => []
	      | x::l' => [{first=x, middle=(#middle full_name), last=(#last full_name)}] @ ret(l')
    in
	[full_name] @ ret(get_substitutions2(l, #first full_name))
    end
	
(* you may assume that Num is always used with values 2, 3, ..., 10
   though it will not really come up *)
datatype suit = Clubs | Diamonds | Hearts | Spades
datatype rank = Jack | Queen | King | Ace | Num of int 
type card = suit * rank

datatype color = Red | Black
datatype move = Discard of card | Draw 

exception IllegalMove

(* put your solutions for problem 2 here *)
