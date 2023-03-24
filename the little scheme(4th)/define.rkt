#lang sicp

(define add1
  (lambda (n)
    (+ 1 n)))

(define length
  (lambda (l)
    (cond
      [(null? l) 0]
      [else (add1 (length (cdr l)))])))

;; 无法停止 最非一般性的递归
(define eternity
  (lambda (x)
    (eternity x)))


;; 能判断空列表 但非空列表没有答案
;; 我们可以给他起个名称 length0
(lambda (l)
  (cond
    [(null? l) 0]
    [else (add1 eternity (cdr l))]))

;; 我们可以这样定义 length1 但是我们有length0么?没有
;; (lambda (l)
;;  (cond
;;    [(null? l) 0]
;;    [else (add1 (length0 (cdr l)))]))
;; 那么 length1 应该是这样的
;; 只是把 length0 替换成了最原始的 lambda
(lambda (l)
  (cond
    [(null? l) 0]
    [else (add1
           ((lambda (l)
              (cond
                [(null? l) 0]
                [else (add1 eternity (cdr l))]))
            (cdr l)))]))

;; 我们可以定义 length2, length3, length4 但是我们不能无限制的这么定义
;; 因为总是会有比我们定义的长度长的list

;; 让我们重新开始
;; step0 这样的还是 length0 么? 绝对是的
((lambda (length)
   (lambda (l)
     (cond
       [(null? l) 0]
       (else
        (add1
         (length (cdr l)))))))
 eternity)

;; 重新写一下 length1
;; step1 最开始的 length1 
(lambda (l)
  (cond
    [(null? l) 0]
    [else
     (add1
      (length0
       (cdr l)))]))

;; step2 我们直接把 length0 传递进去 像刚刚的 length0 那样把 eternity 传递进去
((lambda (length0)
   (lambda (l)
     (cond
       [(null? l) 0]
       [else
        (add1
         (length0
          (cdr l)))])))
   length0)
;; step3 我们没有length0 所以要替换成原始的
((lambda (length0)
   (lambda (l)
     (cond
       [(null? l) 0]
       [else
        (add1
         (length0
          (cdr l)))])))
 ((lambda (length)
    (lambda (l)
      (cond
        [(null? l) 0]
        (else
         (add1
          (length (cdr l)))))))
  eternity))
;; step4 我们需要 length length0 这种名字么？ 并不需要
;; 把 length0 改成 f， 把 length 改成 g
((lambda (f)
   (lambda (l)
     (cond
       [(null? l) 0]
       [else
        (add1
         (f
          (cdr l)))])))
 ((lambda (g)
    (lambda (l)
      (cond
        [(null? l) 0]
        (else
         (add1
          (g (cdr l)))))))
  eternity))
;; 如果我们定义个 length2 这么办？ 我们可以把 step4 的 length1 传递进去
;; 如果我们定义个 length-N 这么办? 我们可以把 length-(N-1) 传递进去
;; 看样子有很多重复的东西
;; 重新开始
;; 这也是 length0 ? 是的 (看看和step0的区别) 只是换了个名字
;; 我们准备用 mk-length 来制造 length
((lambda (mk-length)
   (mk-length eternity))
 (lambda (length) ;; 这是 length0 的定义
   (lambda (l)
     (cond
       [(null? l) 0]
       [else
        (add1
         (length (cdr l)))]))))
;; 这是 length1 么 是的
((lambda (mk-length)
   (mk-length
    (mk-length eternity)))
 (lambda (length)
   (lambda (l)
     (cond
       [(null? l) 0]
       [else
        (add1
         (length (cdr l)))]))))
;; 看着这就是length2 是的
((lambda (mk-length)
   (mk-length
    (mk-length
     (mk-length eternity))))
 (lambda (length)
   (lambda (l)
     (cond
       [(null? l) 0]
       [else
        (add1
         (length (cdr l)))]))))
;; 我们使用 length 时，只需要知道有限数量的 mk-length，但是我们又不知道数量有多少
;; 什么时候会发现没有足够多的 mk-length, 当我们碰上 mk-length 最里面的 eternity 的时候
;; 我们可以在 最里层 eternity 这个位置上， 再创建一个调用 eternity 的 mk-length
;; (mk-length eternity) 有人关心传递的是 eternity 么？ 没有人，(mk-length mk-length) 那就把自己传递给自己
;; 这样才是 length0 么? 是的
((lambda (mk-length)
   (mk-length mk-length))
  (lambda (length)
   (lambda (l)
     (cond
       [(null? l) 0]
       [else
        (add1
         (length (cdr l)))]))))
;; length 名字可以用 mk-length 代替么？ 可以
;; 所有的名字生来平等,但有些名字比其他名字更平等
((lambda (mk-length)
   (mk-length mk-length))
  (lambda (mk-length)
   (lambda (l)
     (cond
       [(null? l) 0]
       [else
        (add1
         (mk-length (cdr l)))]))))

;; 现在 mk-length 被传递给了 mk-length
;; 当我们应用一次 mk-length 我们就得到了 length1
((lambda (mk-length)
   (mk-length mk-length))
  (lambda (mk-length)
   (lambda (l)
     (cond
       [(null? l) 0]
       [else
        (add1
         ((mk-length eternity)
          (cdr l)))]))))


;; example
(((lambda (mk-length)
   (mk-length mk-length))
  (lambda (mk-length)
   (lambda (l)
     (cond
       [(null? l) 0]
       [else
        (add1
         ((mk-length eternity)
          (cdr l)))]))))
 '(apples))

;; 不同大小的 '() 如何重复调用 mk-length ?
;; 确保 将 mk-length 传递给 mk-length 自身 就可以
;; mk-length 最终是什么?length0 ? length1? length2? ...
;; 它是 length
((lambda (mk-length)
   (mk-length mk-length))
  (lambda (mk-length)
   (lambda (l)
     (cond
       [(null? l) 0]
       [else
        (add1
         ((mk-length mk-length)
          (cdr l)))]))))

;; 把(mk-length mk-length) 提取来 单做参数
;; 这样的的 length 像不像 length ?
;; 像 但是无穷无尽
((lambda (mk-length)
   (mk-length mk-length))
 (lambda (mk-length)
    ((lambda (length)
       (lambda (l)
        (cond
          [(null? l) 0]
          [else
           (add1
            (length
             (cdr l)))])))
     (mk-length mk-length))))


;; =>
((lambda (mk-length)
   (mk-length mk-length))
  (lambda (mk-length)
   (lambda (l)
     (cond
       [(null? l) 0]
       [else
        (add1
         (
          ;;
          (lambda (x)
            ((mk-length mk-length) x))
          ;;
          (cdr l)))]))))

;; =>
((lambda (mk-length)
   (mk-length mk-length))
  (lambda (mk-length)
    (
     ;; 这像 length 么 但根本不依赖 mk-length
     (lambda (length)
       (lambda (l)
         (cond
           [(null? l) 0]
           [else
            (add1
             (length       
              (cdr l)))])))
     ;; 
     ;;
     (lambda (x)
       ((mk-length mk-length) x))
     ;;
     )))

;; =>
((lambda (le)
   ((lambda (mk-length)
      (mk-length mk-length))
    (lambda (mk-length)
      (le       
       (lambda (x)
         ((mk-length mk-length) x))))))

     ;; 传递给 le
     (lambda (length)
       (lambda (l)
         (cond
           [(null? l) 0]
           [else
            (add1
             (length       
              (cdr l)))])))
     ;;
     )
;; => 我们把生成 length 的逻辑从length函数中分离出来
(lambda (le)
  ((lambda (mk-length)
     (mk-length mk-length))
   (lambda (mk-length)
     (le       
      (lambda (x)
        ((mk-length mk-length) x))))))
;; => 这个函数有名字么
;; 有 它叫应用序 Y 组合因子(application-order Y combinator)
;; 简化下名字
(lambda (le)
  ((lambda (f)
     (f f))
   (lambda (f)
     (le       
      (lambda (x)
        ((f f) x))))))
;; =>
(lambda (le)
  ((lambda (f)(f f))
   (lambda (f)
     (le (lambda (x) ((f f) x))))))

;; 定义一下
(define Y
  (lambda (le)
    ((lambda (f)(f f))
     (lambda (f)
       (le (lambda (x) ((f f) x)))))))

(define lex
  (Y (lambda (length)
       (lambda (l)
         (cond
           [(null? l) 0]
           [else
            (add1
             (length       
              (cdr l)))])))))
;; example
;; (lex '(1 2 3 5))

