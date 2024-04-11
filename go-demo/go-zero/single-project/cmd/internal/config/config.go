package config

import (
	"github.com/zeromicro/go-zero/core/stores/cache"
	"github.com/zeromicro/go-zero/core/stores/redis"
	"github.com/zeromicro/go-zero/rest"
)

type Config struct {
	rest.RestConf
	DemoTarget string
	DataSource string
	Table      string
	Cache      cache.CacheConf
	JwtAuth    struct {
		AccessSecret string
		AccessExpire int64
	}
	RedisConf redis.RedisConf
}
