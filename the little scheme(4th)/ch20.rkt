#lang sicp

(define atom?
  (lambda (n)
    (and (not (pair? n)) (not (null? n)))))

(define add1
  (lambda (n)
    (+1 n)))

(define sub1
  (lambda (n)
    (- n 1)))

(define call/cc call-with-current-continuation)
(define abort '())
(define global-table '())

(define the-empty-table
  (lambda (name)
    (abort (cons (quote no-answer)
                 (cons name (quote ()))))))

(define lookup
  (lambda (table name)
    (table name)))

;它接受一个名称、一个值和一个表格,并返回一个新的表格。
;新表格首先将其参数与名称进行比较。如果它们相同,则返回该值。
;否则,新表格返回旧表格返回的任何内容。
(define extend
  (lambda (name1 value table)
    (lambda (name2)
      (cond
        ((eq? name2 name1) value)
        (else (table name2))))))

(define define?
  (lambda (e)
    (cond
      ((atom? e) #f)
      ((atom? (car e))
       (eq? (car e) (quote define)))
      (else #f))))



(define *define
  (lambda (e)
    (set! global-table
          (extend
           (name-of e)
           (box
            (the-meaning
             (right-side-of e)))
           global-table))))

(define box
  (lambda (it)
    (lambda (sel)
      (sel it (lambda (new)
                (set! it new))))))
(define setbox
  (lambda (box new)
    (box (lambda (it set) (set new)))))

(define unbox
  (lambda (box)
    (box (lambda (it set) it))))

(define the-meaning
  (lambda (e)
    (meaning e lookup-in-global-table)))

(define lookup-in-global-table
  (lambda (name)
    (lookup global-table name)))

(define meaning
  (lambda (e table)
    ((expression-to-action e)
     e table)))

(define *quote
  (lambda (e table)
    (text-of e)))

(define *identifier
  (lambda (e table)
    (unbox (lookup table e))))



(define *set
  (lambda (e table)
    (setbox
     (lookup table (name-of e))
     (meaning (right-side-of e) table))))
  


(define *lambda
  (lambda (e table)
    
    (lambda (args)
      (beglis (body-of e)
              (multi-extend
               (formals-of e)
               (box-all args)
               table))) ))


(define beglis
  (lambda (es table)
    (cond
      ((null? (cdr es))
       (meaning (car es) table))
      (else
       ((lambda (val)
          (beglis (cdr es) table))
        (meaning (car es) table))))))

(define box-all
  (lambda (vals)
    (cond
      ((null? vals) (quote ()))
      (else (cons (box (car vals)
                       (box-all (cdr vals))))))))



(define multi-extend
  (lambda (names values table)
    (cond
      ((null? names) table)
      (else
       (extend (car names) (car values)
               (multi-extend
                (cdr names)
                (cdr values)
                table))))))

(define *application
  (lambda (e table)
    ((meaning (function-of e) table)
     (evlis (arguments-of e) table))))


(define evlis
  (lambda (args table)
    (cond
      ((null? args) (quote ()))
      (else
       ((lambda (val)
                 (cons val
                       (evlis (cdr args) table)))
        (meaning (car args) table))))))


(define :car
  (lambda (args-in-a-list)
    (car (car args-in-a-list))))

(define a-prim
  (lambda (p)
    (lambda (args-in-a-list)
      (p (car args-in-a-list)))))


(define b-prim
  (lambda (p)
    (lambda (args-in-a-list)
      (p (car args-in-a-list)
         (car (cdr args-in-a-list))))))



(define *cond  
  (lambda (e table)
    (evcon (cond-lines-of e)
           table)))



(define evcon
  (lambda (lines table)
    (cond
      ((else? (question-of (car lines)))
       (meaning (answer-of (car lines)) table))
      ((meaning (question-of (car lines)) table)
       (meaning (answer-of (car lines)) table))
      (else (evcon (cdr lines) table)))))


(define *const
  ((lambda (:cons :car :cdr :null?
                  :eq? :atom?
                  :zero? :add1 :sub1 :number?)
     (lambda (e table)
       (cond
         ((number? e) e)
         ((eq? e #t) #t)
         ((eq? e #f) #f)
         ((eq? e (quote cons)) :cons)
         ((eq? e (quote car)) :car)
         ((eq? e (quote cdr)) :cdr)
         ((eq? e (quote null?)) :null?)
         ((eq? e (quote eq?)) :eq?)
         ((eq? e (quote atom?)) :atom?)
         ((eq? e (quote zero)) :zero?)
         ((eq? e (quote add1)) :add1)
         ((eq? e (quote sub1)) :sub1)
         ((eq? e (quote number?)) :number?))))
   (b-prim cons)
   (a-prim car)
   (a-prim cdr)
   (a-prim null?)
   (b-prim eq?)
   (a-prim atom?)
   (a-prim zero?)
   (a-prim add1)
   (a-prim sub1)
   (a-prim number?)))

(define *letcc
  (lambda (e table)
    (call/cc
     (lambda (skip)
       (beglis (ccbody-of e)
               (extend (name-of e)
                       (box (a-prim skip))
                       table))))))


(define value
  (lambda (e)
    (call/cc
     (lambda (the-end)
       (set! abort the-end)
       (cond ((define? e) (*define e))
             (else (the-meaning e)))))))

(define expression-to-action
  (lambda (e)
    (cond ((atom? e)
           (atom-to-action e))
          (else (list-to-action e)))))

(define atom-to-action
  (lambda (e)
    (cond ((number? e) *const)
          ((eq? e #t) *const)
          ((eq? e #f) *const)
          ((eq? e (quote cons)) *const)
          ((eq? e (quote car)) *const)
          ((eq? e (quote cdr)) *const)
          ((eq? e (quote null?)) *const)
          ((eq? e (quote eq?)) *const)
          ((eq? e (quote atom?)) *const)
          ((eq? e (quote zero?)) *const)
          ((eq? e (quote addl)) *const)
          ((eq? e (quote subl)) *const)
          ((eq? e (quote number?)) *const)
          (else *identifier))))


(define list-to-action
  (lambda (e)
    (cond ((atom? (car e))
           (cond ((eq? (car e) (quote quote))
                  *quote)
                 ((eq? (car e) (quote lambda))
                  *lambda)
                 ((eq? (car e) (quote letcc))
                  *letcc)
                 ((eq? (car e) (quote set!))
                  *set)
                 ((eq? (car e) (quote cond))
                  *cond)
                 (else *application)))
          (else *application))))



(define text-of
  (lambda (x)
    (car (cdr x))))

(define formals-of
  (lambda (x)
    (car (cdr x))))

(define body-of
  (lambda (x)
    (cdr (cdr x))))

(define ccbody-of
  (lambda (x)
    (cdr (cdr x))))

(define name-of
  (lambda (x)
    (car (cdr x))))

(define right-side-of
  (lambda (x)
    (cond
      ((null? (cdr (cdr x))) 0)
      (else (car (cdr (cdr x)))))))

(define cond-lines-of
  (lambda (x)
    (cdr x)))

(define else?
  (lambda (x)
    (cond
      ((atom? x) (eq? x (quote else)))
      (else #f))))

(define question-of
  (lambda (x)
    (car x)))

(define answer-of
  (lambda (x)
    (car (cdr x))))

(define function-of
  (lambda (x)
    (car x)))

(define arguments-of
  (lambda (x)
    (cdr x)))

(value '(define ls
          (cons
           (cons
            (cons 1 (quote ()))
            (quote ()))
           (quote ()))))
;=> (cons ...)  (((1)))

(value '(car (car (car ls))))