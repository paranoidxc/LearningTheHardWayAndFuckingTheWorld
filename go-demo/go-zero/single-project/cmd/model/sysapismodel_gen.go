// Code generated by goctl. DO NOT EDIT.

package model

import (
	"context"
	"database/sql"
	"fmt"
	"strings"

	"github.com/zeromicro/go-zero/core/stores/builder"
	"github.com/zeromicro/go-zero/core/stores/cache"
	"github.com/zeromicro/go-zero/core/stores/sqlc"
	"github.com/zeromicro/go-zero/core/stores/sqlx"
	"github.com/zeromicro/go-zero/core/stringx"
)

var (
	sysApisFieldNames          = builder.RawFieldNames(&SysApis{})
	sysApisRows                = strings.Join(sysApisFieldNames, ",")
	sysApisRowsExpectAutoSet   = strings.Join(stringx.Remove(sysApisFieldNames, "`id`", "`create_at`", "`create_time`", "`created_at`", "`update_at`", "`update_time`", "`updated_at`"), ",")
	sysApisRowsWithPlaceHolder = strings.Join(stringx.Remove(sysApisFieldNames, "`id`", "`create_at`", "`create_time`", "`created_at`", "`update_at`", "`update_time`", "`updated_at`"), "=?,") + "=?"

	cacheSysApisIdPrefix = "cache:sysApis:id:"
)

type (
	sysApisModel interface {
		Insert(ctx context.Context, data *SysApis) (sql.Result, error)
		FindOne(ctx context.Context, id int64) (*SysApis, error)
		Update(ctx context.Context, data *SysApis) error
		Delete(ctx context.Context, id int64) error
		//FindList(ctx context.Context) ([]SysApis, error)
	}

	defaultSysApisModel struct {
		sqlc.CachedConn
		table string
	}

	SysApis struct {
		Id          int64          `db:"id"`
		CreatedAt   sql.NullTime   `db:"created_at"`
		UpdatedAt   sql.NullTime   `db:"updated_at"`
		DeletedAt   sql.NullTime   `db:"deleted_at"`
		Path        sql.NullString `db:"path"`        // api路径
		Description sql.NullString `db:"description"` // api中文描述
		ApiGroup    sql.NullString `db:"api_group"`   // api组
		Method      string         `db:"method"`      // 方法
	}
)

func newSysApisModel(conn sqlx.SqlConn, c cache.CacheConf, opts ...cache.Option) *defaultSysApisModel {
	return &defaultSysApisModel{
		CachedConn: sqlc.NewConn(conn, c, opts...),
		table:      "`sys_apis`",
	}
}

func (m *defaultSysApisModel) Delete(ctx context.Context, id int64) error {
	sysApisIdKey := fmt.Sprintf("%s%v", cacheSysApisIdPrefix, id)
	_, err := m.ExecCtx(ctx, func(ctx context.Context, conn sqlx.SqlConn) (result sql.Result, err error) {
		query := fmt.Sprintf("delete from %s where `id` = ?", m.table)
		return conn.ExecCtx(ctx, query, id)
	}, sysApisIdKey)
	return err
}


func (m *defaultSysApisModel) FindOne(ctx context.Context, id int64) (*SysApis, error) {
	sysApisIdKey := fmt.Sprintf("%s%v", cacheSysApisIdPrefix, id)
	var resp SysApis
	err := m.QueryRowCtx(ctx, &resp, sysApisIdKey, func(ctx context.Context, conn sqlx.SqlConn, v any) error {
		query := fmt.Sprintf("select %s from %s where `id` = ? limit 1", sysApisRows, m.table)
		return conn.QueryRowCtx(ctx, v, query, id)
	})
	switch err {
	case nil:
		return &resp, nil
	case sqlc.ErrNotFound:
		return nil, ErrNotFound
	default:
		return nil, err
	}
}

func (m *defaultSysApisModel) Insert(ctx context.Context, data *SysApis) (sql.Result, error) {
	sysApisIdKey := fmt.Sprintf("%s%v", cacheSysApisIdPrefix, data.Id)
	ret, err := m.ExecCtx(ctx, func(ctx context.Context, conn sqlx.SqlConn) (result sql.Result, err error) {
		query := fmt.Sprintf("insert into %s (%s) values (?, ?, ?, ?, ?)", m.table, sysApisRowsExpectAutoSet)
		return conn.ExecCtx(ctx, query, data.DeletedAt, data.Path, data.Description, data.ApiGroup, data.Method)
	}, sysApisIdKey)
	return ret, err
}

func (m *defaultSysApisModel) Update(ctx context.Context, data *SysApis) error {
	sysApisIdKey := fmt.Sprintf("%s%v", cacheSysApisIdPrefix, data.Id)
	_, err := m.ExecCtx(ctx, func(ctx context.Context, conn sqlx.SqlConn) (result sql.Result, err error) {
		query := fmt.Sprintf("update %s set %s where `id` = ?", m.table, sysApisRowsWithPlaceHolder)
		return conn.ExecCtx(ctx, query, data.DeletedAt, data.Path, data.Description, data.ApiGroup, data.Method, data.Id)
	}, sysApisIdKey)
	return err
}

func (m *defaultSysApisModel) formatPrimary(primary any) string {
	return fmt.Sprintf("%s%v", cacheSysApisIdPrefix, primary)
}

func (m *defaultSysApisModel) queryPrimary(ctx context.Context, conn sqlx.SqlConn, v, primary any) error {
	query := fmt.Sprintf("select %s from %s where `id` = ? limit 1", sysApisRows, m.table)
	return conn.QueryRowCtx(ctx, v, query, primary)
}

func (m *defaultSysApisModel) tableName() string {
	return m.table
}