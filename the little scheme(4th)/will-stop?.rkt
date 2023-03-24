#lang sicp

(define atom?
  (lambda (x)
    (and (not (pair? x)) (not (null? x)))))

;; 无法停止 最非一般性的递归
(define eternity
  (lambda (x)
    (eternity x)))

;; 定义一个 给定函数 是否能停止
;; 假定该 will-stop? 函数能够定义出来
(define will-stop?
  (lambda (f)
    ...))

;; 如果已经定义完 will-stop? 那么
;; => #t
(will-stop? length)

;; => #f
(wiil-stop? eternity)

;; (last-try '()) 的值是什么
;; => unknow
(define last-try
    (lambda (x)
        (and (will-stop? last-try)
            (eternity x))))
;; 如果我们想得到 (last-try '()) 的值
;; 就必须判断 (and (will-stop? last-try) (eternity '())) 的值
;; 该值 依赖于 (will-stop? last-try) 的值
;; (will-stop? last-try) => #f 的话
;; (and #f ...) => #f
;; => last-try 会停止 will-stop? #f 显示 last-try 不应该停止 矛盾
;; 
;; (will-stop? last-try) => #t 的话
;; (and #t ...) => eternity 不会停止
;; => last-try 不会停止  但是 (will-stop? last-try) 假设是 #t 显示 last-try 应该停止 矛盾

;; 我们非常仔细研究了2种可能的情况， 如果我们能够定义 will-stop? 那么 (will-stop? last-try) 必会生成
;; #f 或者 #t。但是实际上做不到，因为要清楚定义 will-stop？做什么，就必须意味着 will-stop?无法被定义
;; will-stop? 能够准确描述 但又不能用我们的语言来定义函数
;; #define 不能用于 will-stop?
;; #(define ...) 是什么?  这是一个有趣的问题