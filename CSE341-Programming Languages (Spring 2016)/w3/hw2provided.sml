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

fun card_color (suit, rank) =
    case suit of Clubs => Black
	       | Spades => Black
	       | _ => Red
    
	
fun card_value (suit, rank) =
    case rank of Ace => 11
	       | Num i => i
	       | _ => 10 
			    
    
fun remove_card (cs, c, e) =
    case cs of [] => raise e
	     | x::cs' => case x = c of
			     true => cs'
			   | false => case remove_card(cs', c, e) of
					   [] => [x]
					 | y :: cs'' => x::y::cs''

								  
fun all_same_color (cs) =
    case cs of
	[] => true
      | x::[] => true
      | x::y::cs' => case card_color(x) = card_color(y) of
			 true => all_same_color(y::cs')
		       | false => false 

fun sum_cards (cs) =
    let
	fun acc_sum_cards (cs, sum) =
	    case cs of
		[] => sum
	      | x::cs' => acc_sum_cards(cs', sum + card_value(x))
	       
    in
	acc_sum_cards(cs, 0)
    end	
   
fun score (cs,goal) =
    let fun cal(cs) = 
	    case (sum_cards(cs), goal) of
		(sum, goal) => case sum > goal of
				   true => 3 * (sum - goal)
				 | _ => goal - sum
    in
	case all_same_color(cs) of
	    true => cal(cs) div 2
	  | false => cal(cs)
    end
	

fun officiate (cs, ms, goal) =
    let
	fun move_list (cs, ms, l) =
	    case ms of
		[] => l
	      | m::ms' => case m of
			      Discard card => move_list (cs, ms', remove_card(l, card, IllegalMove))
			    | Draw => case cs of
					  [] => l
					| c::_ => case sum_cards(c::l) > goal of
						      true => c::l
						    | false => move_list(remove_card(cs,c,IllegalMove), ms', c::l)
	    
    in
	score(move_list(cs, ms, []), goal) 
    end
