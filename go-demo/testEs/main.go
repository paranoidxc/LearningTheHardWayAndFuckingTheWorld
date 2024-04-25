package main

import (
	"bytes"
	"context"
	"encoding/json"
	"fmt"
	"github.com/elastic/go-elasticsearch/v8"
	"github.com/elastic/go-elasticsearch/v8/esutil"
	"github.com/elastic/go-elasticsearch/v8/typedapi/indices/create"
	"github.com/elastic/go-elasticsearch/v8/typedapi/types"
	"github.com/json-iterator/go"
	"log"
	"strconv"
	"time"
)

func main() {
	//es := createClient()
	//createDoc(es)
	//createIdx()

	createDoc(createClient())
	//myTest()
}

func myTest() {
	// ES 配置
	cfg := elasticsearch.Config{
		Addresses: []string{
			"http://localhost:9200",
		},
	}

	// 创建客户端连接
	client, err := elasticsearch.NewTypedClient(cfg)
	if err != nil {
		fmt.Printf("elasticsearch.NewTypedClient failed, err:%v\n", err)
		return
	}

	createIndex(client)
}

// createIndex 创建索引
func createIndex(client *elasticsearch.TypedClient) {
	resp, err := client.Indices.
		Create("my-review-1").
		Do(context.Background())
	if err != nil {
		fmt.Printf("create index failed, err:%v\n", err)
		return
	}
	fmt.Printf("index:%#v\n", resp.Index)
}

func createClient() *elasticsearch.Client {
	es, err := elasticsearch.NewClient(elasticsearch.Config{
		Addresses: []string{"http://localhost:9200"},
		Username:  "elastic",
		Password:  "GUUblVJifbsBSsKwqJlP",
	})
	if err != nil {
		log.Fatalf("Error creating the client: %s", err)
	}
	fmt.Println(es)

	/*esRef, err := elasticsearch.NewTypedClient(elasticsearch.Config{
		Addresses: []string{"http://localhost:9200"},
	})
	if err != nil {
		log.Fatalf("Error creating the client: %s", err)
	}*/

	return es
}

func createIdx() {
	es, err := elasticsearch.NewTypedClient(elasticsearch.Config{
		Addresses: []string{"http://localhost:9200"},
		Username:  "elastic",
		Password:  "GUUblVJifbsBSsKwqJlP",
	})
	if err != nil {
		log.Fatalf("Error creating the client: %s", err)
	}
	fmt.Println(22222)
	ignoreAbove := 256
	keywordProperty := types.NewKeywordProperty()
	keywordProperty.IgnoreAbove = &ignoreAbove

	dateProperty := types.NewDateProperty()
	format := "yyyy/MM/dd||yyyy/MM||MM/dd||yyyy||MM||dd"
	dateProperty.Format = &format
	fmt.Println(3333)
	// index作成
	_, err = es.Indices.Create("sample_index").Request(&create.Request{
		Settings: &types.IndexSettings{
			IndexSettings: map[string]json.RawMessage{
				// 設定項目
				// bulk index里面的数据更新感觉。如果不需要频繁更新，设置得更长会提高性能。
				"refresh_interval": json.RawMessage(`"30s"`),
				// 可取得的最大件数的上限
				"max_result_window": json.RawMessage(`"1000000"`),
			},
		},
		Mappings: &types.TypeMapping{
			Properties: map[string]types.Property{
				// 映射的定义
				"name":       keywordProperty,
				"age":        types.NewIntegerNumberProperty(),
				"is_checked": types.NewBooleanProperty(),
				"created_at": dateProperty,
			},
		},
	}).Do(context.TODO())
	fmt.Println(9999)
	if err != nil {
		log.Fatalf("2222 Error creating the client: %s", err)
	}

	// index削除
	/*_, err = es.Indices.Delete("sample_index").Do(context.TODO())
	if err != nil {
		log.Fatalf("Error deleting the client: %s", err)
	}*/
}

var jsonitier = jsoniter.ConfigCompatibleWithStandardLibrary

type SampleIndexData struct {
	ID        int64  `json:"id"`
	Name      string `json:"name"`
	Age       int    `json:"age"`
	IsChecked bool   `json:"is_checked"`
	CreatedAt string `json:"created_at"`
}

func createDoc(es *elasticsearch.Client) {
	datas := []*SampleIndexData{}
	for i := 1; i <= 100; i++ {
		datas = append(datas, &SampleIndexData{
			ID:        int64(i),
			Name:      fmt.Sprintf("name_%d", i),
			Age:       20,
			IsChecked: true,
			CreatedAt: time.Date(2021, 1, 15, 17, 28, 55, 0, time.Local).Format("2006/01/02"),
		})
	}

	bi, err := esutil.NewBulkIndexer(esutil.BulkIndexerConfig{
		Index:      "sample_index", // The default index name
		Client:     es,             // The Elasticsearch client
		NumWorkers: 1,              // The number of worker goroutines
	})
	if err != nil {
		log.Fatalf("Error creating the indexer: %s", err)
	}

	for _, a := range datas {
		data, err := jsonitier.Marshal(a)
		if err != nil {
			log.Fatalf("Cannot encode article %d: %s", a.ID, err)
		}

		err = bi.Add(
			context.Background(),
			esutil.BulkIndexerItem{
				// Delete时，action为“delete”，body为nil。
				Action:     "index",
				DocumentID: strconv.Itoa(int(a.ID)),
				Body:       bytes.NewReader(data),
				OnSuccess: func(ctx context.Context, item esutil.BulkIndexerItem, res esutil.BulkIndexerResponseItem) {
					fmt.Println("success")
				},
				OnFailure: func(ctx context.Context, item esutil.BulkIndexerItem, res esutil.BulkIndexerResponseItem, err error) {
					fmt.Println("failure")
				},
			},
		)
		if err != nil {
			log.Fatalf("Unexpected error: %s", err)
		}
	}

	if err := bi.Close(context.Background()); err != nil {
		log.Fatalf("Unexpected error: %s", err)
	}

	// 取决于refresh_interval的值，但是如果感觉很长，在所有的index结束后刷新，数据会立即反映出来，所以很好
	/*_, err = esRef.Indices.Refresh().Index("sample_index").Do(context.Background())
	if err != nil {
		log.Fatalf("Error getting response: %s", err)
	}*/
}

/*
cfg := elasticsearch.Config{
Addresses: []string{
"http://localhost:9200",
},
//Username: "elastic",
//Password: "MZJAzhMrnFfjPoesC3Or",
APIKey: "ExySfJFkTrW1VkOfc5L_Lg",
Transport: &http.Transport{
MaxIdleConnsPerHost:   10,
ResponseHeaderTimeout: time.Second,
DialContext:           (&net.Dialer{Timeout: time.Second}).DialContext,
TLSClientConfig: &tls.Config{
MinVersion: tls.VersionTLS12,
},
},
}

client, err := elasticsearch.NewClient(cfg)
if err != nil {
fmt.Println("err", err)
}

fmt.Println(client)

/*
	res, err := client.Indices.Create("my_index")
	if err != nil {
		log.Fatalf("Error creating the index: %s", err)
	}
	defer res.Body.Close()
*/
/*
indexName := "article-index"

termQuery := map[string]interface{}{
"title": "tt",
}
buf, _ := json.Marshal(termQuery)
res, err := client.Search(
client.Search.WithIndex(indexName),
client.Search.WithBody(bytes.NewReader(buf)),
client.Search.WithTrackTotalHits(true),
client.Search.WithPretty(),
)*/

//defer res.Body.Close()
/*if err != nil {
log.Fatalf("Error searching the document: %s", err)
}
fmt.Println(res.String())
*/
/*
	req := esapi.SearchRequest{
		Index: []string{indexName},
		Body:  strings.NewReader(`{"query": {"match": {"title": "tt"}}}`),
	}
	res, err := req.Do(context.Background(), client)
	if err != nil {
		log.Fatalf("查询失败：%s", err)
	}
	defer res.Body.Close()
	// 输出查询结果
	fmt.Println(res.String())
*/
