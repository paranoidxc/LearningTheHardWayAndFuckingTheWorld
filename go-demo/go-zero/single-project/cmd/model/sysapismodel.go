package model

import (
	"context"
	"fmt"
	"github.com/zeromicro/go-zero/core/stores/cache"
	"github.com/zeromicro/go-zero/core/stores/sqlx"
)

var _ SysApisModel = (*customSysApisModel)(nil)

type (
	// SysApisModel is an interface to be customized, add more methods here,
	// and implement the added methods in customSysApisModel.
	SysApisModel interface {
		sysApisModel
		FindList(ctx context.Context) ([]SysApis, error)
	}

	customSysApisModel struct {
		*defaultSysApisModel
	}
)

// NewSysApisModel returns a model for the database table.
func NewSysApisModel(conn sqlx.SqlConn, c cache.CacheConf, opts ...cache.Option) SysApisModel {
	return &customSysApisModel{
		defaultSysApisModel: newSysApisModel(conn, c, opts...),
	}
}

func (m *defaultSysApisModel) FindList(ctx context.Context) ([]SysApis, error) {
	var list []SysApis
	/*
		logc.Info(context.Background(), "FindList Call")
		m.ExecCtx(ctx, func(ctx context.Context, conn sqlx.SqlConn) (result sql.Result, err error) {
			query := fmt.Sprintf("select %s from %s ",  sysApisRows, m.table)
			conn.QueryRowsCtx(ctx, &list, query +" WHERE id >  ? ", 0)
			return nil, nil
		})
		for _, user := range list {
			fmt.Printf("-----%+v\n", user)
		}
	*/

	//return ret, err
	query := fmt.Sprintf("select %s from %s", sysApisRows, m.table)
	m.QueryRowsNoCache(&list, query)
	for _, user := range list {
		fmt.Printf("no cache -----%+v\n", user)
	}
	return list, nil
}
