#lang sicp

(define multirember-o
  (lambda (a lat)
    (cond
      [(null? lat) (quote ())]
      [(eq? (car lat) a)
       (multirember-o a
                      (cdr lat))]
      [else (cons (car lat)
                  (multirember-o a
                                 (cdr lat)))])))




(define multirember
  (lambda (a lat)
    (letrec
        ((mr (lambda (lat)
               (cond
                 [(null? lat) (quote ())]
                 [(eq? a (car lat))
                  (mr (cdr lat))]
                 [else
                  (cons (car lat)
                        (mr (cdr lat)))]))))
      (mr lat))))
                   
(define member?
  (lambda (a lat)
    (cond
      [(null? lat) #f]
      [(eq? (car lat) a) #t]
      [else (member? a (cdr lat))])))

(define union
  (lambda (set1 set2)
    (cond
      [(null? set1) set2]
      [(member? (car set1) set2)
       (union (cdr set1) set2)]
      [else
       (cons (car set1)
             (union (cdr set1) set2))])))

(define union-b
  (lambda (set1 set2)
    (letrec
        (
         (U (lambda (set)
              (cond
                [(null? set) set2]
                [(M? (car set) set2)
                 (U (cdr set))]
                [else (cons (car set)
                            (U (cdr set)))])))
         (M? (lambda (a lat)
               (letrec
                   ((N? (lambda (lat)
                          (cond
                            [(null? lat) #f]
                            [(eq? (car lat) a) #t]
                            [else (N? (cdr lat))]))))
                 (N? lat))))
         )              
      (U set1))))