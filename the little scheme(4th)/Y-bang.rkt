#lang sicp

(define Y-bang
  (lambda (L)
    (let ((h (lambda (l) (quote ()))))
      (set! h
            (L (lambda (arg) (h arg))))
      h)))

(define biz
  (let ((x 0))
    (lambda (f)
      (set! x (inc x))
      (lambda (a)        
        (if  (= a x)
             0
             (f a))))))


;--------------------------------------------
((Y-bang biz) 5)
; => 不会停止
; 最初 x => 0
; set! x = (add1 x)
; x => 1
; 之后 重复调用的一直是

;(lambda (a)        
;  (if  (= a x)
;       0
;       (f a)))

; 这时 a 是 5
;     x 是 1 (不会再变) 
;     f 是 Y-bang 其中的 (L (lambda (arg) (h arg)))


(define test
  (lambda (length)
    (lambda (l)
      (cond
        ((null? l) 0)
        (else (inc (length (cdr l))))))))
;--------------------------------------------
;((Y-bang test) '(a b c d))
; => 4