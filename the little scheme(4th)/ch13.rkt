#lang sicp
;(load "./ch12")
;(#%require "ch12.rkt")

(define member?
  (lambda (a lat)
    (cond
      [(null? lat) #f]
      [(eq? (car lat) a) #t]
      [else (member? a (cdr lat))])))

(define intersect
  (lambda (set1 set2)
    (letrec
        ((I (lambda (set)              
              (cond
                [(null? set) (quote ())]
                [(member? (car set) set2)
                 (cons (car set)
                       (I (cdr set)))]
                [else (I (cdr set))]))))
      (I set1))))


(define intersectall
  (lambda (lset)
    (letrec
        ((intersectall
          (lambda (lset)            
            (cond
              ((null? (cdr lset)) (car lset))
              (else (intersect (car lset)
                               (intersectall (cdr lset))))))))
      (cond
        [(null? lset) (quote ())]
        [else (intersectall lset)]))))
;(intersectall '((a b c) ( c a d e) ( e f g h a b)))

;(intersectall '((3 ma and) () (3 di ha)))

(define intersectall-b
  (lambda (lset)
    (call-with-current-continuation
     (lambda (hop)
       (letrec
           ((A (lambda (lset)
                 (cond
                   [(null? (car lset))
                    (hop (quote ()))]
                   [(null? (cdr lset))
                    (car lset)]
                   [else
                    (intersect (car lset)
                               (A (cdr lset)))]))))
         (cond
           [(null? lset) (quote ())]
           [else (A lset)]))))))



(define intersectall-c
  (lambda (lset)
    (letcc
        (lambda (hop)
          (letrec
              ((A (lambda (lset)
                    (cond
                      [(null? (car lset))
                       (hop (quote ()))]
                      [(null? (cdr lset))
                       (car lset)]
                      [else (I (car lset)
                               (A (cdr lset)))])))
               (I (lambda (s1 s2)
                    (letrec
                        ((J (lambda (s1)
                              (cond
                                [(null? s1) (quote ())]
                                [(member? (car s1) s2)
                                 (cons (car s1)
                                       (J (cdr s1)))]                              
                                [else (J (cdr s1))]))))
                      (cond
                        [(null? s2) (hop (quote ()))]
                        [else (J s1)])))))
            (cond
              [(null? lset) (quote ())]
              [else (A lset)]))))))
 ;(intersectall-c '((a b c) ( c a d e) ( e f g h a b)))     

(define rember
  (lambda (a lat)
    (letrec
        ((R (lambda (lat)
              (cond
                [(null? lat) (quote ())]
                [(eq? (car lat) a)
                 (cdr lat)]
                [else (cons (car lat)
                            (R (cdr lat)))]))))
      (R lat))))

; (rember 'ro '(no sp spa be ro po ot ri))
(define rember-beyond-first
  (lambda (a lat)
    (letrec
        ((R (lambda (lat)
              (cond
                [(null? lat) (quote ())]
                [(eq? (car lat) a)
                 (quote ())]
                [else (cons (car lat)
                            (R (cdr lat)))]))))
      (R lat))))
 ; (rember-beyond-first 'ro '(no sp spa be ro po ot ri))

(define letcc call-with-current-continuation)
(define rember-upto-last
  (lambda (a lat)
    (letcc
        (lambda (skip)
          (letrec
              ((R (lambda (lat)
                    (cond
                      [(null? lat) (quote ())]
                      [(eq? (car lat) a)
                       (skip (R (cdr lat)))]
                      [else
                       (cons (car lat)
                             (R (cdr lat)))]))))            
            (R lat))))))

;(rember-upto-last 'co '(co ch mi car des cho va ie cr ge ch ca mo co gi ch chip bro))